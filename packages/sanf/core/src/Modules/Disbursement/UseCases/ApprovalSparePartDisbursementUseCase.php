<?php

namespace Sanf\Core\Modules\Disbursement\UseCases;

use Illuminate\Support\Carbon;
use NbsPhp\Core\Exceptions\ResourceNotFoundException;
use Sanf\Api\Modules\Disbursement\Constants\SparePartDisbursementApprovalAction;
use Sanf\Core\Modules\Asset\NbsFile;
use Sanf\Core\Modules\Disbursement\Enums\SparePartDisbursementStatusEnum;
use Sanf\Core\Modules\Disbursement\Models\SparePartDisbursementDocumentModel;
use Sanf\Core\Modules\Disbursement\Models\SparePartDisbursementInvoiceModel;
use Sanf\Core\Modules\Disbursement\Payloads\ApprovalSparePartDisbursementPayload;
use Sanf\Core\Modules\Disbursement\Repositories\SparePartDisbursementRepositoryInterface;
use Sanf\Integration\Modules\SanfCore\Payloads\SanfCoreSubmitSparePartFinancingBankAccountPayload;
use Sanf\Integration\Modules\SanfCore\Payloads\SanfCoreSubmitSparePartFinancingDocumentPayload;
use Sanf\Integration\Modules\SanfCore\Payloads\SanfCoreSubmitSparePartFinancingInvoicePayload;
use Sanf\Integration\Modules\SanfCore\Payloads\SanfCoreSubmitSparePartFinancingPayload;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

final class ApprovalSparePartDisbursementUseCase
{
    protected SparePartDisbursementRepositoryInterface $repository;
    protected SanfCoreApiClientV2 $sanfCoreApiClient;

    public function __construct(
        SparePartDisbursementRepositoryInterface $repository,
        SanfCoreApiClientV2 $sanfCoreApiClient
    ) {
        $this->repository = $repository;
        $this->sanfCoreApiClient = $sanfCoreApiClient;
    }

    public function execute(ApprovalSparePartDisbursementPayload $payload)
    {
        $disbursement = $this->repository->find([
            'xid' => $payload->disbursementXid,
            'customer_id_sanfind' => $payload->profileXid,
            'status_id' => SparePartDisbursementStatusEnum::WAITING_CUSTOMER,
        ]);

        if ($disbursement === null) {
            throw new ResourceNotFoundException('Disbursement not found');
        }

        if ($payload->action === SparePartDisbursementApprovalAction::REJECT_SELECTED) {
            $this->repository->rejectInvoices(
                [
                    'disbursement_xid' => $payload->disbursementXid,
                    'customer_id_sanfind' => $payload->profileXid,
                ],
                $payload->invoiceXids,
            );

            $this->repository->approveInvoicesWithExclusion(
                [
                    'disbursement_xid' => $payload->disbursementXid,
                    'customer_id_sanfind' => $payload->profileXid,
                ],
                $payload->invoiceXids,
            );

            $this->repository->update(
                ['xid' => $disbursement->xid],
                [
                    'note' => $payload->note,
                    'customer_confirmed_at' => Carbon::now(),
                    'status_id' => SparePartDisbursementStatusEnum::NEED_REVIEW,
                ]
            );

            return;
        }

        $this->repository->approveInvoicesWithExclusion(
            [
                'disbursement_xid' => $payload->disbursementXid,
                'customer_id_sanfind' => $payload->profileXid,
            ],
            [],
        );

        $this->repository->update(
            ['xid' => $disbursement->xid],
            [
                'customer_confirmed_at' => Carbon::now(),
                'status_id' => SparePartDisbursementStatusEnum::WAITING_VALIDATION,
            ]
        );

        $invoices = $this->repository->listInvoice([
            'disbursement_xid' => $payload->disbursementXid,
            'customer_id_sanfind' => $payload->profileXid,
        ]);

        $disbursementBatch = $this->repository->findBatch([
            'id' => $disbursement->disbursement_batch_id,
        ]);

        $documents = $this->repository->listUploadedDocument([
            'disbursement_batch_id' => $disbursementBatch->id,
        ]);

        $this->sanfCoreApiClient->submitSparePartFinancing(
            new SanfCoreSubmitSparePartFinancingPayload([
                'BATCH_ID' => $disbursement->batch_number,
                'SUPPLIER_ID' => $disbursement->supplier_id,
                'INVOICE' => $invoices->map(fn ($item) => $this->mappingInvoice($item))->toArray(),
                'BANK_ACCOUNT' => new SanfCoreSubmitSparePartFinancingBankAccountPayload([
                    'BANK_ID' => $disbursementBatch->bank_id,
                    'OWNER' => $disbursementBatch->bank_owner,
                    'PROVIDER' => $disbursementBatch->bank_provider,
                    'ACCOUNT_NUMBER' => $disbursementBatch->bank_account_number,
                ]),
                'DOCUMENTS' => $documents->map(fn ($item) => $this->mappingDocument($item))->toArray(),
            ])
        );
    }

    protected function mappingInvoice(SparePartDisbursementInvoiceModel $invoice)
    {
        $statusId = '02';
        $statusDesc = 'Ditolak';

        if ($invoice->status_id == SparePartDisbursementStatusEnum::APPROVED) {
            $statusId = '01';
            $statusDesc = 'Disetujui';
        }

        return new SanfCoreSubmitSparePartFinancingInvoicePayload([
            'CUST_ID' => $invoice->customer_id_sanfind,
            'NO_INVOICE' => $invoice->invoice_number,
            'TANGGAL_INVOICE' => $invoice->invoice_date->format('m-d-Y'),
            'CURRENCY' => config('payment.currency'),
            'TOTAL_INVOICE' => $invoice->invoice_amount,
            'STATUS_INVOICE_ID' => $statusId,
            'STATUS_INVOICE_DESC' => $statusDesc,
        ]);
    }

    protected function mappingDocument(SparePartDisbursementDocumentModel $document)
    {
        $file = new NbsFile($document->doc_file);

        return new SanfCoreSubmitSparePartFinancingDocumentPayload([
            'DOC_ID' => $document->doc_id,
            'FILE_PATH' => $file->dirname,
            'FILE_NAME' => $file->originName,
        ]);
    }
}
