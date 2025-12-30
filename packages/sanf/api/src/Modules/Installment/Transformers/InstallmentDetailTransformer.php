<?php

namespace Sanf\Api\Modules\Installment\Transformers;

use Illuminate\Support\Carbon;
use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\Installment\Responses\FindInstallmentResponse;
use Sanf\Core\Modules\Installment\Responses\InstallmentOutstandingResponse;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

final class InstallmentDetailTransformer extends TransformerAbstract
{
    /**
     * @param FindInstallmentResponse $installment
     */
    public function transform($installment): array
    {
        return [
            'due_date' => Carbon::parse($installment->dueDate, SanfCoreApiClientV2::DEFAULT_TIMEZONE)->timestamp,
            'total_amount' => $installment->totalAmount,
            'subtotal_installment' => $installment->subtotalInstallment,
            'principal_loan' => $installment->principalLoan,
            'interest_amount' => $installment->interestAmount,
            'penalty_fee' => $installment->penaltyFee,
            'status' => $installment->status,
            'payment_xid' => $installment->paymentXid,
            'sequence_no' => $installment->sequenceNo,
            'sequence_total' => $installment->sequenceTotal,
            'outstanding_installments' => array_map(fn (InstallmentOutstandingResponse $item) => [
                'due_date' => $item->dueDate,
                'total' => $item->total,
                'principal_loan' => $item->pricipalLoan,
                'interest_amount' => $item->interestAmount,
                'penalty_fee' => $item->penaltyFee,
            ], $installment->outstandingInstallments),
            'contract' => [
                'contract_no' => $installment->contract->contractNo,
                'contract_date' => $installment->contract->contractDate,
                'supplier_name' => $installment->contract->supplierName,
                'financing_type_id' => $installment->contract->financingTypeId,
                'financing_type_desc' => $installment->contract->financingTypeDesc,
                'financing_facility_id' => $installment->contract->financingFacilityId,
                'financing_facility_desc' => $installment->contract->financingFacilityDesc,
                'financing_method_id' => $installment->contract->financingMethodId,
                'financing_method_desc' => $installment->contract->financingMethodDesc,
                'financing_amount' => $installment->contract->financingAmount,
                'down_payment' => $installment->contract->downPayment,
                'paid_amount' => $installment->contract->paidAmount,
                'outstanding_amount' => $installment->contract->outstandingAmount,
                'tenor_value' => $installment->contract->tenorValue,
                'tenor_unit' => $installment->contract->tenorUnit,
                'status_id' => $installment->contract->statusId,
                'status_desc' => $installment->contract->statusDesc,
                'due_date' => $installment->contract->dueDate,
                'completed_date' => $installment->contract->completedDate,
                'daily_interest_rate' => $installment->contract->interestRate,
                'plafond_type' => $installment->contract->plafondType,
            ],
            'e_statement_file' => $installment->eStatementFile ? [
                'file_name' => $installment->eStatementFile->fileName,
                'file_type' => $installment->eStatementFile->fileType,
                'url' => $installment->eStatementFile->url,
            ] : null,
        ];
    }
}
