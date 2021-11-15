<?php


namespace Sanf\Api\Modules\Plafond\Transformers;

use League\Fractal\TransformerAbstract;

final class PlafondHistoryTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'type' => [
                'id' => $dto->type->id,
                'name' => $dto->type->name,
            ],
            'status' => [
                'id' => $dto->status->getValue(),
                'name' => $dto->status->getTranslation()
            ],
            'updated_at' => unix_timestamp($dto->updatedAt),
            'current_balance' => $dto->currentBalance,
            'added_balance' => $dto->addedBalance,
            'submitted_balance' => $dto->submittedBalance,
        ];
    }
}
