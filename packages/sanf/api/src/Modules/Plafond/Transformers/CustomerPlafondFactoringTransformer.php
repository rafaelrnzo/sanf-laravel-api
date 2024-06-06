<?php

namespace Sanf\Api\Modules\Plafond\Transformers;

use League\Fractal\TransformerAbstract;

final class CustomerPlafondFactoringTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'id' => $dto->id,
            'name' => $dto->name,
            'code' => $dto->code,
            'email' => $dto->email,
            'cust_id' => $dto->customer_id,
        ];
    }
}
