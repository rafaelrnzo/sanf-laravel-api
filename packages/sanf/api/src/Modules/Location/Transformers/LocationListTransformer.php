<?php

namespace Sanf\Api\Modules\Location\Transformers;


use League\Fractal\TransformerAbstract;

class LocationListTransformer extends TransformerAbstract
{

    public function transform($dto)
    {
        return [
            'xid' => $dto->location_code,
            'name' => $dto->name,
            'level' => $dto->level,
            'created_at' => unix_timestamp($dto->created_at),
            'version' => $dto->version,
        ];
    }
}