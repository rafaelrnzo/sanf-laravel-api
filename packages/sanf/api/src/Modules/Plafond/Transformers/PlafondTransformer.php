<?php

namespace Sanf\Api\Modules\Plafond\Transformers;

use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

final class PlafondTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'xid' => $dto->id,
            'type' => [
                'id' => $dto->type->id,
                'name' => $dto->type->name,
            ],
            'updated_at' => unix_timestamp($dto->updatedAt),
            'remaining_balance' => $dto->remainingBalance,
            'used_balance' => $dto->usedBalance,
            'histories' => fractal($dto->histories, new PlafondHistoryTransformer())->serializeWith(ArraySerializer::class),
        ];
    }
}
