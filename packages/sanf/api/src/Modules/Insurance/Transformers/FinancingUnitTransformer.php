<?php

namespace Sanf\Api\Modules\Insurance\Transformers;

use League\Fractal\TransformerAbstract;

final class FinancingUnitTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'polis_no' => $dto->polisNo,
            'serial_no' => $dto->serialNo,
            'brand_type_model' => $dto->brandTypeModel,
            'year' => $dto->year,
        ];
    }
}
