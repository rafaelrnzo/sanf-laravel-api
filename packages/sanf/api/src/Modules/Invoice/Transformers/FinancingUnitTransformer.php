<?php

namespace Sanf\Api\Modules\Invoice\Transformers;

use League\Fractal\TransformerAbstract;

final class FinancingUnitTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'contract_no' => $dto->contractNo,
            'serial_no' => $dto->serialNo,
            'brand_type_model' => $dto->brandTypeModel,
            'year' => $dto->year,
        ];
    }
}
