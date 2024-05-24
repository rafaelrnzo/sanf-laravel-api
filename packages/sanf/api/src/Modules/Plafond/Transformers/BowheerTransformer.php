<?php

namespace Sanf\Api\Modules\Plafond\Transformers;

use League\Fractal\TransformerAbstract;

final class BowheerTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'id' => $dto->customer_id,
            'name' => $dto->customer_name,
            'code' => $dto->customer_code,
            'email' => $dto->customer_mail,
        ];
    }
}
