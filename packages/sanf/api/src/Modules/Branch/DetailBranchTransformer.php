<?php

namespace Sanf\Api\Modules\Branch;

use League\Fractal\TransformerAbstract;

class DetailBranchTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'id' => (int) $dto->id,
            'name' => (string) $dto->name,
            'address' => (string) $dto->address,
            'msisdn' => (string) $dto->msisdn,
            'msisdn_alternative' => (string) $dto->msisdn_alternative,
            'email' => (string) $dto->email,
            'latitude' => (float) $dto->latitude,
            'longitude' => (float) $dto->longitude,
        ];
    }
}
