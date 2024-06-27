<?php

namespace Sanf\Core\Modules\Plafond\UseCases;

use Carbon\Carbon;
use Firebase\Auth\Token\Exception\InvalidToken;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Exception\MessagingException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use NbsPhp\Notification\Repositories\UserNotificationRepositoryInterface;
use NbsPhp\Notification\Services\PushNotificationServiceInterface;
use Sanf\Core\Modules\Notification\Exceptions\NotificationInvalidException;
use Sanf\Core\Modules\Notification\NotificationTypeEnum;
use Sanf\Core\Modules\Plafond\Dtos\DisbursementAllocationFormRequest;
use Sanf\Core\Modules\Plafond\Dtos\DisbursementDocumentFormRequest;
use Sanf\Core\Modules\Plafond\Dtos\DisbursementInvoiceFormRequest;
use Sanf\Core\Modules\Plafond\Dtos\PlafondDisbursementFormRequest;
use Sanf\Core\Modules\Plafond\Dtos\ReadPlafondDisbursementRequestDto;
use Sanf\Core\Modules\Plafond\Enums\PlafondDisbursementStatusEnum;
use Sanf\Core\Modules\Plafond\Events\PlafondDisbursementSubmittedEvent;
use Sanf\Core\Modules\Plafond\Exceptions\PlafondDisbursementIsNotRevisionException;
use Sanf\Core\Modules\Plafond\Exceptions\PlafondDisbursementNotFoundException;
use Sanf\Core\Modules\Plafond\Queries\ReadPlafondDisbursementEloquentBuilder;
use Sanf\Core\Modules\Plafond\Repositories\PlafondDisbursementRepositoryInterface;
use Sanf\Core\Modules\User\Exceptions\ProfileNotFoundException;
use Sanf\Core\Modules\User\Repositories\ProfileRepositoryInterface;

final class UpdatePlafondDisbursementUseCase implements ApplicationServiceInterface
{
    private const DEFAULT_AMOUNT = 0.0;
    private const DEFAULT_VERSION = 1;

    private $coreClientRepository;
    private $disbursementRepository;
    private $userNotificationRepository;
    private $pushNotificationService;

    public function __construct(
        PlafondDisbursementRepositoryInterface $disbursementRepository,
        ProfileRepositoryInterface $coreClientRepository,
        UserNotificationRepositoryInterface $userNotificationRepository,
        PushNotificationServiceInterface $pushNotificationService
    ) {
        $this->coreClientRepository = $coreClientRepository;
        $this->disbursementRepository = $disbursementRepository;
        $this->userNotificationRepository = $userNotificationRepository;
        $this->pushNotificationService = $pushNotificationService;
    }

    /**
     * @return mixed
     */
    public function execute($formRequest = null)
    {
        /** @var PlafondDisbursementFormRequest $formRequest */
        $userGuzzleEntity = $this->coreClientRepository->findById($formRequest->clientId);
        if (is_null($userGuzzleEntity)) {
            throw new ProfileNotFoundException("User {$formRequest->clientId} not found");
        }

        $dto = new ReadPlafondDisbursementRequestDto([
            'user_id' => $formRequest->userId,
            'profile_xid' => $formRequest->clientId,
            'plafond_xid' => $formRequest->plafondId,
            'disbursement_xid' => $formRequest->disbursementId,
        ]);

        $plafondDisbursements = $this->disbursementRepository->query(new ReadPlafondDisbursementEloquentBuilder($dto));
        if (count($plafondDisbursements) === 0) {
            throw new PlafondDisbursementNotFoundException();
        }

        $disbursementData = $plafondDisbursements[0];
        if ($disbursementData->status_id !== PlafondDisbursementStatusEnum::REVISION) {
            throw new PlafondDisbursementIsNotRevisionException();
        }

        $submissionXid = nano_id();
        $disbursementNo = $disbursementData->disbursement_no;
        $disbursementStatus = (new PlafondDisbursementStatusEnum(PlafondDisbursementStatusEnum::SUBMIT));
        $disbursementSubmissionStatus = (new PlafondDisbursementStatusEnum(PlafondDisbursementStatusEnum::SUBMIT));
        $version = $disbursementData->version + 1;

        $disbursementModel = $this->disbursementRepository->updateDisbursement($disbursementData->id, [
            'status_id' => $disbursementStatus->getValue(),
            'status' => $disbursementStatus->getLabel(),
            'client_amount' => $formRequest->totalInvoiceAmount,
            'updated_at' => date('Y-m-d H:i:s'),
            'version' => $version,
        ]);

        $allocationsInput = [];
        foreach ($formRequest->allocations as $allocation) {
            /* @var DisbursementAllocationFormRequest $allocation */
            $allocationsInput[] = [
                'xid' => nano_id(),
                'plafond_disbursement_id' => $disbursementModel->id,
                'bank_id' => $allocation->id,
                'owner' => $allocation->name,
                'provider' => $allocation->provider,
                'account_no' => $allocation->accountNo,
                'is_default' => $allocation->isDefault,
                'amount' => $allocation->amount,
                'notes' => $allocation->notes,
                'order_no' => $allocation->orderNo,
                'version' => $version,
            ];
        }

        $invoicesInput = [];
        foreach ($formRequest->invoices as $invoiceIndex => $invoice) {
            /* @var DisbursementInvoiceFormRequest $invoice */
            $invoicesInput[$invoiceIndex] = [
                'xid' => nano_id(),
                'plafond_disbursement_id' => $disbursementModel->id,
                'origin_name' => $invoice->originName,
                'file_name' => $invoice->fileName,
                'path' => config('image-path.plafond.disbursement.invoice_document'),
                'metadata' => json_encode($this->invoiceDocumentMovingFile($invoice->fileName)),
                'document_no' => $invoice->invoiceNo,
                'document_date' => $invoice->invoiceDate,
                'invoice_amount' => $invoice->invoiceAmount,
                'tax_amount' => $invoice->taxAmount,
                'vat_amount' => $invoice->vatAmount,
                'backharge_amount' => $invoice->backhargeAmount,
                'other_amount' => $invoice->otherAmount,
                'total_amount' => $invoice->totalAmount,
                'order_no' => $invoice->orderNo,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
                'version' => $version,
            ];

            foreach ($invoice->photos as $photoIndex => $photo) {
                /* @var DisbursementDocumentFormRequest $photo */
                $invoicesInput[$invoiceIndex]['photos'][] = [
                    'xid' => nano_id(),
                    'plafond_disbursement_id' => $disbursementModel->id,
                    'origin_name' => $photo->origin,
                    'file_name' => $photo->name,
                    'path' => config('image-path.plafond.disbursement.invoice_document'),
                    'metadata' => json_encode($this->invoiceDocumentMovingFile($photo->name)),
                    'order_no' => $photoIndex + 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => null,
                    'version' => $version,
                ];
            }
        }

        $documentsInput = [];
        foreach ($formRequest->otherDocument as $documentIndex => $document) {
            /* @var DisbursementDocumentFormRequest $document */
            $documentsInput[] = [
                'xid' => nano_id(),
                'plafond_disbursement_id' => $disbursementModel->id,
                'origin_name' => $document->origin,
                'file_name' => $document->name,
                'path' => config('image-path.plafond.disbursement.other_document'),
                'metadata' => json_encode($this->otherDocumentMovingFile($document->name)),
                'order_no' => $documentIndex + 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
                'version' => $version,

            ];
        }
        $paymentAccDocument = ($formRequest->paymentAccDocument->origin) ? $this->paymentAccDocumentMovingFile($formRequest) : [];
        $submissionModel = $this->disbursementRepository->createSubmission([
            'xid' => $submissionXid,
            'plafond_disbursement_id' => $disbursementModel->id,
            'client_amount' => $formRequest->totalInvoiceAmount,
            'customer_amount' => ($disbursementData->customer_amount > 0) ? $disbursementData->customer_amount : self::DEFAULT_AMOUNT,
            'invoice_snapshot' => json_encode($invoicesInput),
            'allocation_snapshot' => json_encode($allocationsInput),
            'other_doc_snapshot' => json_encode($documentsInput),
            'payment_acc_doc_origin_name' => $formRequest->paymentAccDocument->origin,
            'payment_acc_doc_file_name' => $formRequest->paymentAccDocument->name,
            'payment_acc_doc_path' => $paymentAccDocument['path'] ?? null,
            'payment_acc_doc_metadata' => json_encode($paymentAccDocument),
            'status_id' => $disbursementSubmissionStatus->getValue(),
            'status' => $disbursementSubmissionStatus->getLabel(),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => null,
            'version' => $version,
            'user_updated_by' => json_encode([
                'source' => 'client',
                'user' => [
                    'id' => $userGuzzleEntity->getCustomerId(),
                    'name' => $userGuzzleEntity->getFullName(),
                    'email' => $userGuzzleEntity->getEmail(),
                ],
            ]),
        ]);

        foreach ($allocationsInput as $allocation) {
            $allocation['submission_id'] = $submissionModel->id;

            $this->disbursementRepository->createAllocation($allocation);
        }

        foreach ($invoicesInput as $invoice) {
            $invoice['submission_id'] = $submissionModel->id;
            $photos = [];
            if (isset($invoice['photos'])) {
                $photos = $invoice['photos'];
                unset($invoice['photos']);
            }

            $invoiceModel = $this->disbursementRepository->createInvoice($invoice);

            foreach ($photos as $photo) {
                $photo['submission_id'] = $submissionModel->id;
                $photo['invoice_id'] = $invoiceModel->id;
                $this->disbursementRepository->createInvoicePhoto($photo);
            }
        }

        foreach ($documentsInput as $document) {
            $document['submission_id'] = $submissionModel->id;

            $this->disbursementRepository->createDocument($document);
        }

        // send notification
        // TODO create self service of send notification using event service
        $fcmTokens = $this->userNotificationRepository->getFcmTokens($formRequest->userId);
        $data = [
            'xid' => nano_id(),
            'title' => __('Pengajuan anda berhasil'),
            'subtitle' => __('Sukses pengajuan pencairan plafond'),
            'body' => __('Pengajuan pencairan plafond and telah berhasil dikirim dan sedang dalam proses.'),
            'type' => (string) NotificationTypeEnum::INFO,
            'screen' => '',
            'published_at' => Carbon::now(),
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        ];

        try {
            $this->userNotificationRepository->create([
                'xid' => $data['xid'],
                'type' => (int) $data['type'],
                'user_id' => $formRequest->userId,
                'data' => $data,
            ]);
        } catch (QueryException $exception) {
            if ($exception->getCode() == '23505') {
                throw new NotificationInvalidException('ID not unique');
            }
            throw $exception;
        }
        foreach (array_unique($fcmTokens) as $fcmToken) {
            try {
                $this->pushNotificationService->sendToDevice($fcmToken, $data);
            } catch (InvalidToken $exception) {
                $this->userNotificationRepository->deleteFcmToken($fcmToken);
                report($exception);
            } catch (MessagingException $exception) {
                $this->userNotificationRepository->deleteFcmToken($fcmToken);
                report($exception);
            }
        }

        $mailContent = (object) [
            'fullName' => $userGuzzleEntity->getFullName(),
            'email' => $userGuzzleEntity->getEmail(),
            'bowheer' => $formRequest->bouwheer,
            'disbursementNo' => $disbursementNo,
            'invoiceCount' => count($invoicesInput),
            'totalAmount' => $formRequest->totalInvoiceAmount,
            'createdAt' => $disbursementModel->created_at,
        ];
        event(new PlafondDisbursementSubmittedEvent($mailContent));

        return $disbursementModel;
    }

    /**
     * @param string $filename
     * @param string $temporaryPath
     * @param string $path
     * @return array
     */
    protected function moveFile(string $filename, string $temporaryPath, string $path)
    {
        $fileExist = Storage::disk('minio_post')->exists("{$temporaryPath}{$filename}");
        if ($fileExist) {
            Storage::disk('minio_post')->move("{$temporaryPath}{$filename}", "{$path}{$filename}");
        }

        $metadata = Storage::disk('minio_post')->getMetaData("{$path}{$filename}");

        return [
            'file_name' => $filename,
            'directory' => $path,
            'path' => "{$path}{$filename}",
            'mime_type' => $metadata['mimetype'],
            'size' => $metadata['size'],
        ];
    }

    /**
     * @param PlafondDisbursementFormRequest $formRequest
     * @return array
     */
    private function paymentAccDocumentMovingFile(PlafondDisbursementFormRequest $formRequest)
    {
        $temporaryPath = config('image-path.temp');
        $path = config('image-path.plafond.disbursement.payment_acc_document');
        $filename = $formRequest->paymentAccDocument->name;

        return $this->moveFile($filename, $temporaryPath, $path);
    }

    /**
     * @param string $filename
     * @return array
     */
    private function invoiceDocumentMovingFile(string $filename)
    {
        $temporaryPath = config('image-path.temp');
        $path = config('image-path.plafond.disbursement.invoice_document');

        return $this->moveFile($filename, $temporaryPath, $path);
    }

    /**
     * @param string $filename
     * @return array
     */
    private function otherDocumentMovingFile(string $filename)
    {
        $temporaryPath = config('image-path.temp');
        $path = config('image-path.plafond.disbursement.other_document');

        return $this->moveFile($filename, $temporaryPath, $path);
    }
}
