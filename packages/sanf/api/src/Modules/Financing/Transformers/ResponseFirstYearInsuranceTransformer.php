<?php

namespace Sanf\Api\Modules\Financing\Transformers;

use League\Fractal\TransformerAbstract;

class ResponseFirstYearInsuranceTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'financing_method_id' => (string) $dto->financingMethodId,
            'financing_method_name' => (string) $dto->financingMethodName,
            'unit_amount' => (float) $dto->unitAmount,
            'first_year_insurance_amount' => (float) number_format($dto->firstYearInsuranceAmount, 2, '.', ''),
        ];
    }
}
