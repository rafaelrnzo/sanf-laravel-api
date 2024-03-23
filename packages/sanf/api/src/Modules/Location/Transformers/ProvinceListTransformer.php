<?php

namespace Sanf\Api\Modules\Location\Transformers;

use League\Fractal\TransformerAbstract;

class ProvinceListTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'country_id' => (string) $dto->country_id,
            'province_id' => (string) $dto->province_id,
            'province_name' => (string) $dto->name,
        ];
    }
}
