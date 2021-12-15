<?php

namespace Sanf\Api\Modules\Prepayment\Transformers;

use League\Fractal\TransformerAbstract;

final class PrepaymentSimulationItemTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'description' => $dto->description,
            'amount' => $dto->amount,
        ];
    }
}
