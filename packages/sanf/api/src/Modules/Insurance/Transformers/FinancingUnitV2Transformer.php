<?php

namespace Sanf\Api\Modules\Insurance\Transformers;

use League\Fractal\TransformerAbstract;

/**
 * @since CR2025
 */
final class FinancingUnitV2Transformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'contract_no' => $dto->contractNo,
            'polis_no' => $dto->polisNo,
            'serial_no' => $dto->serialNo,
            'brand_type_model' => $dto->brandTypeModel,
            'year' => $dto->year,
            'city_id' => $dto->cityId,
            'city_name' => $dto->cityName,
        ];
    }
}
