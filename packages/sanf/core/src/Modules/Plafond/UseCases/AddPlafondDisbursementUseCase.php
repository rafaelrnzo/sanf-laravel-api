<?php

namespace Sanf\Core\Modules\Plafond\UseCases;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Storage;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Financing\Exceptions\FinancingApplicationLimitExceedException;
use Sanf\Core\Modules\Plafond\Dtos\PlafondDisbursementFormRequest;
use Sanf\Core\Modules\Plafond\Enums\PlafondDisbursementStatusEnum;
use Sanf\Core\Modules\Plafond\Events\PlafondDisbursementSubmittedMailEvent;
use Sanf\Core\Modules\Plafond\Events\PlafondDisbursementSubmittedNotificationEvent;
use Sanf\Core\Modules\Plafond\Repositories\PlafondDisbursementRepositoryInterface;
use Sanf\Core\Modules\User\Exceptions\ProfileNotFoundException;
use Sanf\Core\Modules\User\Repositories\ProfileRepositoryInterface;

final class AddPlafondDisbursementUseCase implements ApplicationServiceInterface
{
    private const DEFAULT_AMOUNT = 0.0;
    private const DEFAULT_VERSION = 1;

    private $coreClientRepository;
    private $disbursementRepository;
    private $submitToCoreUseCase;

    public function __construct(
        PlafondDisbursementRepositoryInterface $disbursementRepository,
        ProfileRepositoryInterface $coreClientRepository,
        SubmitPlafondDisbursementCoreUseCase $submitToCoreUseCase
    ) {
        $this->coreClientRepository = $coreClientRepository;
        $this->disbursementRepository = $disbursementRepository;
        $this->submitToCoreUseCase = $submitToCoreUseCase;
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

        $disbursementXid = nano_id();
        $disbursementNo = $this->generateDisbursementNo();
        $disbursementStatus = (new PlafondDisbursementStatusEnum(PlafondDisbursementStatusEnum::SUBMIT));
        $submissionXid = nano_id();

        $disbursementModel = $this->disbursementRepository->createDisbursement([
            'xid' => $disbursementXid,
            'plafond_id' => $formRequest->plafondId,
            'disbursement_no' => $disbursementNo,
            'client_id' => $userGuzzleEntity->getCustomerId(),
            'client_name' => $userGuzzleEntity->getFullName(),
            'client_mail' => $userGuzzleEntity->getEmail(),
            'customer_id' => $formRequest->bouwheer->custId ?? $userGuzzleEntity->getCustomerId(),
            'customer_bowheer_id' => $formRequest->bouwheer->id,
            'customer_name' => $formRequest->bouwheer->name,
            'customer_mail' => $formRequest->bouwheer->email,
            'customer_code' => $formRequest->bouwheer->code,
            'customer_review' => $formRequest->customerReview,
            'status_id' => $disbursementStatus->getValue(),
            'status' => $disbursementStatus->getLabel(),
            'client_amount' => $formRequest->totalInvoiceAmount,
            'customer_amount' => self::DEFAULT_AMOUNT,
            'admin_amount' => self::DEFAULT_AMOUNT,
            'plafond_submission_xid' => $submissionXid,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => null,
            'customer_updated_at' => null,
            'admin_updated_at' => null,
            'version' => self::DEFAULT_VERSION,
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
                'version' => self::DEFAULT_VERSION,
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
                'version' => self::DEFAULT_VERSION,
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
                    'version' => self::DEFAULT_VERSION,
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
                'version' => self::DEFAULT_VERSION,

            ];
        }
        $paymentAccDocument = ($formRequest->paymentAccDocument->origin) ? $this->paymentAccDocumentMovingFile($formRequest) : [];
        $submissionModel = $this->disbursementRepository->createSubmission([
            'xid' => $submissionXid,
            'plafond_disbursement_id' => $disbursementModel->id,
            'client_amount' => $formRequest->totalInvoiceAmount,
            'customer_amount' => self::DEFAULT_AMOUNT,
            'invoice_snapshot' => json_encode($invoicesInput),
            'allocation_snapshot' => json_encode($allocationsInput),
            'other_doc_snapshot' => json_encode($documentsInput),
            'payment_acc_doc_origin_name' => $formRequest->paymentAccDocument->origin,
            'payment_acc_doc_file_name' => $formRequest->paymentAccDocument->name,
            'payment_acc_doc_path' => $paymentAccDocument['path'] ?? null,
            'payment_acc_doc_metadata' => json_encode($paymentAccDocument),
            'status_id' => $disbursementStatus->getValue(),
            'status' => $disbursementStatus->getLabel(),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => null,
            'version' => self::DEFAULT_VERSION,
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

        if ($formRequest->customerReview === false) {
            $coreFormRequest = (object) [
                'userId' => $formRequest->userId,
                'clientId' => $formRequest->clientId,
                'plafondId' => $formRequest->plafondId,
                'disbursementId' => $disbursementXid,
            ];
            $submitToCore = $this->submitToCoreUseCase->execute($coreFormRequest);
        }

        $mailContent = (object) [
            'fullName' => $userGuzzleEntity->getFullName(),
            'email' => (object) [
                'client' => $userGuzzleEntity->getEmail(),
                'customer' => $formRequest->bouwheer->email,
            ],
            'bowheer' => $formRequest->bouwheer,
            'disbursementNo' => $disbursementNo,
            'invoiceCount' => count($invoicesInput),
            'totalAmount' => $formRequest->totalInvoiceAmount,
            'createdAt' => $disbursementModel->created_at,
            'webPartnerUrl' => config('web-partner.base_url') . "/plafond/disbursements/{$disbursementXid}/submissions/{$submissionXid}",
        ];
        $notificationContent = (object) [
            'userId' => $formRequest->userId,
            'clientId' => $formRequest->clientId,
            'client' => $userGuzzleEntity->getFullName(),
            'bowheerId' => $formRequest->bouwheer->id,
            'bowheer' => $formRequest->bouwheer->name,
            'disbursementXid' => $disbursementXid,
            'submissionXid' => $submissionXid,
        ];

        event(new PlafondDisbursementSubmittedMailEvent($mailContent));
        event(new PlafondDisbursementSubmittedNotificationEvent($notificationContent));

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
        $fileExistInTempPath = Storage::exists("{$temporaryPath}{$filename}");
        if ($fileExistInTempPath) {
            $fileExistInNewPath = Storage::exists("{$path}{$filename}");
            if ($fileExistInNewPath === false) {
                Storage::move("{$temporaryPath}{$filename}", "{$path}{$filename}");
            }
        }

        $metadata = Storage::getMetaData("{$path}{$filename}");

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

    private function generateDisbursementNo()
    {
        $now = CarbonImmutable::now();
        $year = $now->format('Y');
        $month = $now->format('m');
        $count = $this->disbursementRepository->countInMonth($now);
        $width = 6;
        if ($count >= 999999) {
            throw new FinancingApplicationLimitExceedException();
        }

        return "{$month}{$year}" . str_pad((string) $count + 1, $width, '0', STR_PAD_LEFT);
    }
}
