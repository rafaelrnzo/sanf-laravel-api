<?php

namespace Sanf\Api\Modules\Financing\Transformers;

use League\Fractal\TransformerAbstract;

class ResponseFinancingSimulationTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        $properties = [
            'unit_amount' => $dto->unit_amount ?? null,
            'financing_amount' => $dto->financing_amount ?? null,
            'invoice_amount' => $dto->invoice_amount ?? null,
            'down_payment_percentage' => $dto->down_payment_percentage ?? null,
            'down_payment_amount' => $dto->down_payment_amount ?? null,
            'retention_percentage' => $dto->retention_percentage ?? null,
            'retention_amount' => $dto->retention_amount ?? null,
            'first_installment_type' => $dto->first_installment_type ?? null,
            'interest_percentage' => $dto->interest_percentage ?? null,
            'tenor' => $dto->tenor ?? null,
            'first_year_insurance_amount' => $dto->first_year_insurance_amount ?? null,
            'admin_fee_amount' => $dto->admin_fee_amount ?? null,
            'provision_amount' => $dto->provision_amount ?? null,
            'installment_per_month' => $dto->installment_per_month ?? null,
            'credit_insurance_amount' => $dto->credit_insurance_amount ?? null,
            'total_credit_amount' => $dto->total_credit_amount ?? null,
            'first_installment_amount' => $dto->first_installment_amount ?? null,
            'total_first_payment_amount' => $dto->total_first_payment_amount ?? null,
            'diskonto_amount' => $dto->diskonto_amount ?? null,
            'disbursement_amount' => $dto->disbursement_amount ?? null,
        ];

        return array_filter($properties, function ($data) {
            return is_null($data) === false;
        });
    }
}
