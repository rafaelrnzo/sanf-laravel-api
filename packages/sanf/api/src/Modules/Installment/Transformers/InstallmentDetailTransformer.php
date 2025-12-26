<?php

namespace Sanf\Api\Modules\Installment\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\Installment\Responses\FindInstallmentResponse;

final class InstallmentDetailTransformer extends TransformerAbstract
{
    /**
     * @param FindInstallmentResponse $installment
     */
    public function transform($installment): array
    {
        return [
            'total_amount' => $installment->totalAmount,
            'due_date' => $installment->dueDate,
            'penalty_fee' => $installment->penaltyFee,
            'principal_loan' => $installment->principalLoan,
            'interest_amount' => $installment->interestAmount,
            'down_payment' => $installment->downPayment,
            'paid_down_payment' => $installment->paidDownPayment,
            'paid_amount' => $installment->paidAmount,
            'status' => $installment->status,
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
                'tenor_value' => $installment->contract->tenorValue,
                'tenor_unit' => $installment->contract->tenorUnit,
                'status_id' => $installment->contract->statusId,
                'status_desc' => $installment->contract->statusDesc,
                'due_date' => $installment->contract->dueDate,
                'completed_date' => $installment->contract->completedDate,
                'interest_rate' => $installment->contract->interestRate,
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
