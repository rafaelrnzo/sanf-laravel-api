<?php

namespace Sanf\Api\Modules\Plafond\Transformers;

use League\Fractal\TransformerAbstract;

final class PlafondSimpleTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'xid' => $dto->id,
            'type' => [
                'id' => $dto->type->id,
                'name' => $dto->type->title,
            ],
            'remaining_balance' => $dto->remainingBalance,
            'used_balance' => $dto->usedBalance,
            'updated_at' => unix_timestamp($dto->updatedAt),
        ];
    }
}
