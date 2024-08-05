<?php

namespace Sanf\Api\Modules\Financing\Transformers;

use League\Fractal\TransformerAbstract;

class ResponseProvisionTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'financing_method_id' => (string) $dto->financingMethodId,
            'financing_method_name' => (string) $dto->financingMethodName,
            'unit_amount' => (float) $dto->unitAmount,
            'down_payment_percentage' => $dto->downPaymentPercentage,
            'down_payment_amount' => $dto->downPaymentAmount,
            'tenor' => $dto->tenor,
            'first_year_insurance_amount' => (float) $dto->firstYearInsuranceAmount,
            'provision_amount' => (float) number_format($dto->provisionAmount, 2, '.', ''),
        ];
    }
}
