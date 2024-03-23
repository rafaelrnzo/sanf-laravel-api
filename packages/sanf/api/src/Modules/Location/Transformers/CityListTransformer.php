<?php

namespace Sanf\Api\Modules\Location\Transformers;

use League\Fractal\TransformerAbstract;

class CityListTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'country_id' => (string) $dto->country_id,
            'province_id' => (string) $dto->province_id,
            'city_id' => (string) $dto->city_id,
            'city_name' => (string) $dto->city_name,
        ];
    }
}
