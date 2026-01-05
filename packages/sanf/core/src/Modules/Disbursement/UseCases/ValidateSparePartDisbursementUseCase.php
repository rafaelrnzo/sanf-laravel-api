<?php

namespace Sanf\Core\Modules\Disbursement\UseCases;

use Illuminate\Support\Collection;
use NbsPhp\Core\Exceptions\ResourceNotFoundException;
use Sanf\Core\Modules\Disbursement\Enums\SparePartDisbursementStatusEnum;
use Sanf\Core\Modules\Disbursement\Exceptions\SparePartDisbrusementValidated;
use Sanf\Core\Modules\Disbursement\Payloads\ValidateSparePartDisbursementPayload;
use Sanf\Core\Modules\Disbursement\Repositories\SparePartDisbursementRepositoryInterface;

final class ValidateSparePartDisbursementUseCase
{
    protected SparePartDisbursementRepositoryInterface $repository;

    public function __construct(SparePartDisbursementRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(ValidateSparePartDisbursementPayload $payload)
    {
        if ($payload->validation_complete === false) {
            return;
        }

        $batch = $this->repository->findBatch(['batch_number' => $payload->batch_id]);

        if ($batch === null) {
            throw new ResourceNotFoundException("Batch {$payload->batch_id} not found");
        }

        if ($batch->is_validated) {
            throw new SparePartDisbrusementValidated();
        }

        $disbursements = $this->repository->get(
            ['id', 'batch_number', 'customer_id'],
            ['batch_number' => $payload->batch_id],
            ['invoices:id,disbursement_id,invoice_number,invoice_amount']
        );

        $this->validateCustomers($disbursements, $payload);

        $disbursementCollection = $disbursements->keyBy(fn ($item) => $item->customer_id);

        $batchValidCount = 0;
        $batchValidAmount = 0.0;

        foreach ($payload->customers as $customerPayload) {
            $disbursement = $disbursementCollection->get($customerPayload->cust_id);

            $validCount = 0;
            $validAmount = 0.0;

            $invoiceCollection = $disbursement->invoices->keyBy(fn ($item) => $item->invoice_number);

            foreach ($customerPayload->invoices as $invoicePayload) {
                $invoice = $invoiceCollection->get($invoicePayload->no_invoice);

                $isApproved = $invoicePayload->status;
                $statusId = $isApproved ? SparePartDisbursementStatusEnum::APPROVED : SparePartDisbursementStatusEnum::REJECTED;

                if ($isApproved) {
                    $validCount++;
                    $validAmount += (float) $invoice->invoice_amount;
                }

                $detail = $invoice->detail ?? [];
                $detail['validation'] = $invoicePayload->toArray();

                $this->repository->updateInvoice([
                    ['id', '=', $invoice->id],
                ], [
                    'customer_id_sanfind' => $customerPayload->cust_id_sanfind,
                    'status_id' => $statusId,
                    'detail' => $detail,
                ]);
            }

            $this->repository->update([
                ['id', '=', $disbursement->id],
            ], [
                'valid_invoice_count' => $validCount,
                'total_valid_invoice_amount' => $validAmount,
                'payment_type' => $customerPayload->tipe_pembayaran_id,
                'status_id' => SparePartDisbursementStatusEnum::DRAFT,
                'plafond_no' => $customerPayload->no_plafond,
                'customer_id_sanfind' => $customerPayload->cust_id_sanfind,
                'validation_status_code' => $customerPayload->status_code,
                'validation_status_message' => $customerPayload->status_message,
            ]);

            $batchValidCount += $validCount;
            $batchValidAmount += $validAmount;
        }

        $this->repository->updateBatch([
            ['id', '=', $batch->id],
        ], [
            'valid_invoice_count' => $batchValidCount,
            'total_valid_invoice_amount' => $batchValidAmount,
            'is_validated' => true,
        ]);
    }

    private function validateCustomers(Collection $disbursements, ValidateSparePartDisbursementPayload $payload)
    {
        $disbursementCustomerIds = array_unique(
            array_map('strval', $disbursements->pluck('customer_id')->toArray())
        );
        $payloadCustomerIds = array_unique(
            array_map('strval', array_pluck($payload->customers, 'cust_id'))
        );

        $missingInPayload = array_diff($disbursementCustomerIds, $payloadCustomerIds);

        if (!empty($missingInPayload)) {
            $missingIds = implode(', ', $missingInPayload);

            throw new ResourceNotFoundException(
                "Customer IDs {$missingIds} are missing from payload"
            );
        }

        $missingInDisbursement = array_diff($payloadCustomerIds, $disbursementCustomerIds);

        if (!empty($missingInDisbursement)) {
            $missingIds = implode(', ', $missingInDisbursement);

            throw new ResourceNotFoundException(
                "Customer IDs {$missingIds} are do not match with our records"
            );
        }

        $payloadCustomers = collect($payload->customers)
            ->keyBy(fn ($customer) => (string) $customer->cust_id);

        foreach ($disbursements as $disbursement) {
            $customerId = (string) $disbursement->customer_id;
            $payloadCustomer = $payloadCustomers->get($customerId);

            if ($payloadCustomer === null) {
                continue;
            }

            $disbursementInvoiceNumbers = array_unique(
                array_map('strval', $disbursement->invoices->pluck('invoice_number')->toArray())
            );
            $payloadInvoiceNumbers = array_unique(
                array_map(fn ($invoice) => (string) $invoice->no_invoice, $payloadCustomer->invoices)
            );

            $missingInvoiceInPayload = array_diff($disbursementInvoiceNumbers, $payloadInvoiceNumbers);

            if (!empty($missingInvoiceInPayload)) {
                $missingInvoices = implode(', ', $missingInvoiceInPayload);

                throw new ResourceNotFoundException(
                    "Invoices {$missingInvoices} for customer {$customerId} are missing from payload"
                );
            }

            $missingInvoiceInDisbursement = array_diff($payloadInvoiceNumbers, $disbursementInvoiceNumbers);

            if (!empty($missingInvoiceInDisbursement)) {
                $missingInvoices = implode(', ', $missingInvoiceInDisbursement);

                throw new ResourceNotFoundException(
                    "Invoices {$missingInvoices} for customer {$customerId} do not match our records"
                );
            }
        }
    }
}
