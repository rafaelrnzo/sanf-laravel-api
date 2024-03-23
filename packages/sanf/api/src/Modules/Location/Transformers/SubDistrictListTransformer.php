<?php

namespace Sanf\Api\Modules\Location\Transformers;

use League\Fractal\TransformerAbstract;

class SubDistrictListTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'country_id' => (string) $dto->country_id,
            'province_id' => (string) $dto->province_id,
            'city_id' => (string) $dto->city_id,
            'district_name' => (string) $dto->district_name,
            'subdistrict_name' => (string) $dto->sub_district_name,
            'postcode' => (string) $dto->postcode,
        ];
    }
}
