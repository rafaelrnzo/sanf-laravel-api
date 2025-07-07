<?php

namespace Sanf\Core\Modules\Plafond\UseCases;

use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Financing\Exceptions\FinancingApplicationLimitExceedException;
use Sanf\Core\Modules\Plafond\Dtos\PlafondDisbursementFormRequest;
use Sanf\Core\Modules\Plafond\Enums\PlafondDisbursementStatusEnum;
use Sanf\Core\Modules\Plafond\Enums\PlafondTypeEnum;
use Sanf\Core\Modules\Plafond\Events\PlafondDisbursementSubmittedMailEvent;
use Sanf\Core\Modules\Plafond\Events\PlafondDisbursementSubmittedNotificationEvent;
use Sanf\Core\Modules\Plafond\Repositories\PaymentAccelarationDocumentRepositoryInterface;
use Sanf\Core\Modules\Plafond\Repositories\PlafondDisbursementRepositoryInterface;
use Sanf\Core\Modules\Plafond\Repositories\PlafondRepositoryInterface;
use Sanf\Core\Modules\User\Exceptions\ProfileNotFoundException;
use Sanf\Core\Modules\User\Repositories\ProfileRepositoryInterface;

final class AddPlafondDisbursementUseCase implements ApplicationServiceInterface
{
    private const DEFAULT_AMOUNT = 0.0;
    private const DEFAULT_VERSION = 1;

    private $coreClientRepository;
    private $disbursementRepository;
    private $submitToCoreUseCase;
    private $paymentAccDocRepository;
    private $plafondRepository;

    public function __construct(
        PlafondDisbursementRepositoryInterface $disbursementRepository,
        ProfileRepositoryInterface $coreClientRepository,
        SubmitPlafondDisbursementCoreUseCase $submitToCoreUseCase,
        PaymentAccelarationDocumentRepositoryInterface $paymentAccDocRepository,
        PlafondRepositoryInterface $plafondRepository
    ) {
        $this->coreClientRepository = $coreClientRepository;
        $this->disbursementRepository = $disbursementRepository;
        $this->submitToCoreUseCase = $submitToCoreUseCase;
        $this->paymentAccDocRepository = $paymentAccDocRepository;
        $this->plafondRepository = $plafondRepository;
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
        $disbursementStatus = ($formRequest->customerReview) ? (new PlafondDisbursementStatusEnum(PlafondDisbursementStatusEnum::SUBMIT)) : (new PlafondDisbursementStatusEnum(PlafondDisbursementStatusEnum::DONE));
        $submissionXid = nano_id();
        $customerId = $userGuzzleEntity->getCustomerId();
        $clientName = $userGuzzleEntity->getFullName();

        $disbursementModel = $this->disbursementRepository->createDisbursement([
            'xid' => $disbursementXid,
            'plafond_id' => $formRequest->plafondId,
            'disbursement_no' => $disbursementNo,
            'client_id' => $userGuzzleEntity->getCustomerId(),
            'client_name' => $clientName,
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
            'user_id' => $formRequest->userId,
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
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ];
        }

        $invoicesInput = [];
        $invoiceFileSequence = 0;
        foreach ($formRequest->invoices as $invoiceIndex => $invoice) {
            $movedInvoiceFile = $this->invoiceDocumentMovingFile($invoice->fileName, "Invoice-$customerId", ++$invoiceFileSequence);
            /* @var DisbursementInvoiceFormRequest $invoice */
            $invoicesInput[$invoiceIndex] = [
                'xid' => nano_id(),
                'plafond_disbursement_id' => $disbursementModel->id,
                'origin_name' => $invoice->originName,
                'file_name' => $movedInvoiceFile['file_name'] ?? $invoice->fileName,
                'path' => config('image-path.plafond.disbursement.invoice_document'),
                'metadata' => json_encode($movedInvoiceFile),
                'document_no' => $invoice->invoiceNo,
                'document_date' => $invoice->invoiceDate,
                'invoice_amount' => $invoice->invoiceAmount,
                'tax_amount' => $invoice->taxAmount,
                'vat_amount' => $invoice->vatAmount,
                'backharge_amount' => $invoice->backhargeAmount,
                'other_amount' => $invoice->otherAmount,
                'total_amount' => $invoice->totalAmount,
                'order_no' => $invoice->orderNo,
                'due_at' => $invoice->dueAt,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
                'version' => self::DEFAULT_VERSION,
            ];

            foreach ($invoice->photos as $photoIndex => $photo) {
                $movedInvoicePhotoFile = $this->invoiceDocumentMovingFile($photo->name, "Invoice-$customerId", ++$invoiceFileSequence);
                /* @var DisbursementDocumentFormRequest $photo */
                $invoicesInput[$invoiceIndex]['photos'][] = [
                    'xid' => nano_id(),
                    'plafond_disbursement_id' => $disbursementModel->id,
                    'origin_name' => $photo->origin,
                    'file_name' => $movedInvoicePhotoFile['file_name'] ?? $photo->name,
                    'path' => config('image-path.plafond.disbursement.invoice_document'),
                    'metadata' => json_encode($movedInvoicePhotoFile),
                    'order_no' => $photoIndex + 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => null,
                    'version' => self::DEFAULT_VERSION,
                ];
            }
        }

        $documentsInput = [];
        foreach ($formRequest->otherDocument as $documentIndex => $document) {
            $movedOtherDocumentFile = $this->otherDocumentMovingFile($document->name, "FilePendukung-$customerId", $documentIndex + 1);
            /* @var DisbursementDocumentFormRequest $document */
            $documentsInput[] = [
                'xid' => nano_id(),
                'plafond_disbursement_id' => $disbursementModel->id,
                'origin_name' => $document->origin,
                'file_name' => $movedOtherDocumentFile['file_name'] ?? $document->name,
                'path' => config('image-path.plafond.disbursement.other_document'),
                'metadata' => json_encode($movedOtherDocumentFile),
                'order_no' => $documentIndex + 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
                'version' => self::DEFAULT_VERSION,

            ];
        }
        $paymentAccDocument = ($formRequest->paymentAccDocument->origin) ? $this->paymentAccDocumentMovingFile($formRequest, "Percepatan-$customerId") : [];
        $submissionModel = $this->disbursementRepository->createSubmission([
            'xid' => $submissionXid,
            'plafond_disbursement_id' => $disbursementModel->id,
            'client_amount' => $formRequest->totalInvoiceAmount,
            'customer_amount' => self::DEFAULT_AMOUNT,
            'invoice_snapshot' => json_encode($invoicesInput),
            'allocation_snapshot' => json_encode($allocationsInput),
            'other_doc_snapshot' => json_encode($documentsInput),
            'payment_acc_doc_origin_name' => $formRequest->paymentAccDocument->origin,
            'payment_acc_doc_file_name' => $paymentAccDocument['file_name'] ?? $formRequest->paymentAccDocument->name,
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
            'payment_acc_doc_no' => $formRequest->paymentAccDocument->no,
            'payment_acc_doc_date' => $formRequest->paymentAccDocument->date,
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

        $companyInfo = $this->paymentAccDocRepository->getCompanyInfoForEmail(
            $formRequest->clientId,
            $formRequest->plafondId
        );

        $invoiceTableData = $this->prepareInvoiceTableData($invoicesInput, $clientName);

        $clientBankData = $this->getClientBankData($customerId, PlafondTypeEnum::FACTORING);

        $allocationsBankData = $this->getClientBankDataFromAllocations($allocationsInput);

        $paymentAccDocumentAttachment = null;
        if (!empty($paymentAccDocument)) {
            $paymentAccDocumentAttachment = [
                'path' => $paymentAccDocument['path'] ?? null,
                'file_name' => $paymentAccDocument['file_name'] ?? $formRequest->paymentAccDocument->name,
                'origin_name' => $formRequest->paymentAccDocument->origin,
                'mime_type' => $paymentAccDocument['mime_type'] ?? 'application/pdf',
            ];
        }

        $invoiceDocuments = [];
        $invoicePhotos = [];
        foreach ($invoicesInput as $invoice) {
            $invoiceDocuments[] = [
                'path' => $invoice['path'] . $invoice['file_name'],
                'file_name' => $invoice['file_name'],
                'origin_name' => $invoice['origin_name'],
                'mime_type' => json_decode($invoice['metadata'], true)['mime_type'] ?? 'application/pdf',
            ];

            if (isset($invoice['photos'])) {
                foreach ($invoice['photos'] as $photo) {
                    $invoicePhotos[] = [
                        'path' => $photo['path'] . $photo['file_name'],
                        'file_name' => $photo['file_name'],
                        'origin_name' => $photo['origin_name'],
                        'mime_type' => json_decode($photo['metadata'], true)['mime_type'] ?? 'image/jpeg',
                    ];
                }
            }
        }

        $otherDocuments = [];
        foreach ($documentsInput as $document) {
            $otherDocuments[] = [
                'path' => $document['path'] . $document['file_name'],
                'file_name' => $document['file_name'],
                'origin_name' => $document['origin_name'],
                'mime_type' => json_decode($document['metadata'], true)['mime_type'] ?? null,
            ];
        }

        $mailContent = (object) [
            'fullName' => $clientName,
            'email' => (object) [
                'client' => $userGuzzleEntity->getEmail(),
                'customer' => $formRequest->bouwheer->email,
            ],
            'bowheer' => $formRequest->bouwheer,
            'company_info' => $companyInfo,
            'disbursementNo' => $disbursementNo,
            'invoiceCount' => count($invoicesInput),
            'totalAmount' => $formRequest->totalInvoiceAmount,
            'createdAt' => $disbursementModel->created_at,
            'webPartnerUrl' => config('web-partner.base_url') . "plafond/disbursements/{$disbursementXid}/submissions/{$submissionXid}",
            'customerReview' => $formRequest->customerReview,
            'paymentAccDocumentNo' => $formRequest->paymentAccDocument->no,
            'paymentAccDocumentDate' => $formRequest->paymentAccDocument->date,
            'invoices' => $invoiceTableData,
            'clientTargetBank' => $clientBankData,
            'allocationsTargetBank' => $allocationsBankData,
            'plafond_id' => $formRequest->plafondId,
            'payment_acc_document' => $paymentAccDocumentAttachment,
            'invoice_documents' => $invoiceDocuments,
            'invoice_photos' => $invoicePhotos,
            'other_documents' => $otherDocuments,
        ];
        $notificationContent = (object) [
            'userId' => $formRequest->userId,
            'clientId' => $formRequest->clientId,
            'client' => $clientName,
            'bowheerId' => $formRequest->bouwheer->id,
            'bowheer' => $formRequest->bouwheer->name,
            'disbursementXid' => $disbursementXid,
            'submissionXid' => $submissionXid,
            'customerReview' => $formRequest->customerReview,
            'totalAmount' => $formRequest->totalInvoiceAmount,
        ];

        event(new PlafondDisbursementSubmittedMailEvent($mailContent));
        event(new PlafondDisbursementSubmittedNotificationEvent($notificationContent));

        return $disbursementModel;
    }

    /**
     * @param string $filename
     * @param string $temporaryPath
     * @param string $path
     * @param string $newFileName @since CR2025 normalisasi nama file
     * @param int $sequence @since CR2025 nomor urut
     * @return array
     */
    protected function moveFile(string $filename, string $temporaryPath, string $path, string $newFileName, int $sequence)
    {
        $fileExistInTempPath = Storage::disk('minio_post')->exists("{$temporaryPath}{$filename}");
        if ($fileExistInTempPath) {
            $fileTimestamp = Carbon::now('Asia/Jakarta')->format('YmdHis');
            $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
            $newFileName .= "-$sequence-$fileTimestamp.$fileExtension"; // Invoice-8624PROSM-123-20250702125959.pdf
            $fileExistInNewPath = Storage::disk('minio_post')->exists("{$path}{$newFileName}");
            if ($fileExistInNewPath === false) {
                Storage::disk('minio_post')->move("{$temporaryPath}{$filename}", "{$path}{$newFileName}");
            }
        } else {
            $newFileName = $filename;
        }

        $metadata = Storage::disk('minio_post')->getMetaData("{$path}{$newFileName}");

        return [
            'file_name' => $newFileName,
            'directory' => $path,
            'path' => "{$path}{$newFileName}",
            'mime_type' => $metadata['mimetype'],
            'size' => $metadata['size'],
        ];
    }

    /**
     * @param PlafondDisbursementFormRequest $formRequest
     * @param string $newFileName @since CR2025 normalisasi nama file
     * @return array
     */
    private function paymentAccDocumentMovingFile(PlafondDisbursementFormRequest $formRequest, string $newFileName)
    {
        $temporaryPath = config('image-path.temp');
        $path = config('image-path.plafond.disbursement.payment_acc_document');
        $filename = $formRequest->paymentAccDocument->name;

        return $this->moveFile($filename, $temporaryPath, $path, $newFileName, 1);
    }

    /**
     * @param string $filename
     * @param string $newFileName @since CR2025 normalisasi nama file
     * @param int $sequence @since CR2025 nomor urut
     * @return array
     */
    private function invoiceDocumentMovingFile(string $filename, string $newFileName, int $sequence)
    {
        $temporaryPath = config('image-path.temp');
        $path = config('image-path.plafond.disbursement.invoice_document');

        return $this->moveFile($filename, $temporaryPath, $path, $newFileName, $sequence);
    }

    /**
     * @param string $filename
     * @param string $newFileName @since CR2025 normalisasi nama file
     * @param int $sequence @since CR2025 nomor urut
     * @return array
     */
    private function otherDocumentMovingFile(string $filename, string $newFileName, int $sequence)
    {
        $temporaryPath = config('image-path.temp');
        $path = config('image-path.plafond.disbursement.other_document');

        return $this->moveFile($filename, $temporaryPath, $path, $newFileName, $sequence);
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

    /**
     * Prepare invoice data for email table.
     * @param array $invoicesInput
     * @param string $clientName
     * @return array
     */
    private function prepareInvoiceTableData(array $invoicesInput, string $clientName): array
    {
        $tableData = [];

        foreach ($invoicesInput as $index => $invoice) {
            $tableData[] = [
                'no' => $index + 1,
                'customer' => $clientName,
                'tanggal_invoice' => date('d M Y', strtotime($invoice['document_date'])),
                'due_date' => date('d M Y', strtotime($invoice['due_at'])),
                'no_invoice' => $invoice['document_no'],
                'dpp' => 'Rp. ' . number_format($invoice['invoice_amount'], 0, ',', '.'),
                'ppn' => 'Rp. ' . number_format($invoice['vat_amount'], 0, ',', '.'),
                'pph23' => 'Rp. ' . number_format($invoice['tax_amount'], 0, ',', '.'),
                'total' => 'Rp. ' . number_format($invoice['total_amount'], 0, ',', '.'),
            ];
        }

        return $tableData;
    }

    /**
     * @return array
     */
    private function getClientBankData($customerId, $plafondCode): array
    {
        $plafonds = $this->plafondRepository->getPlafondFactoringV2ByProfile($customerId, $plafondCode);

        $bankSections = [];
        $groupedBanks = [];

        foreach ($plafonds as $plafond) {
            $virtualAccounts = $plafond->getVirtualAccounts();

            foreach ($virtualAccounts as $virtualAccount) {
                $key = $virtualAccount->getDescription() . '_' . $virtualAccount->getVaId();

                if (!isset($groupedBanks[$key])) {
                    $groupedBanks[$key] = [
                        'title' => strtoupper($virtualAccount->getDescription()),
                        'Nomor Rekening' => $virtualAccount->getVaId(),
                        'Atas Nama' => $virtualAccount->getAccountName(),
                    ];
                }
            }
        }

        foreach ($groupedBanks as $bank) {
            $bankSections[] = $bank;
        }

        return $bankSections;
    }

    /**
     * @param array $allocationsInput
     * @return array
     */
    private function getClientBankDataFromAllocations(array $allocationsInput): array
    {
        $bankSections = [];
        $groupedAllocations = [];

        foreach ($allocationsInput as $allocation) {
            $key = $allocation['provider'] . '_' . $allocation['account_no'];
            if (!isset($groupedAllocations[$key])) {
                $groupedAllocations[$key] = $allocation;
            }
        }

        foreach ($groupedAllocations as $allocation) {
            $bankSections[] = [
                'title' => strtoupper($allocation['provider']),
                'Nomor Rekening' => $allocation['account_no'],
                'Atas Nama' => $allocation['owner'],
            ];
        }

        return $bankSections;
    }
}
