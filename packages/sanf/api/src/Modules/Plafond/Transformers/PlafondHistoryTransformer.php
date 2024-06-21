<?php

namespace Sanf\Api\Modules\Plafond\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\Plafond\Enums\PlafondStatusEnum;

final class PlafondHistoryTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        switch ($dto->status->getValue()) {
            case PlafondStatusEnum::REJECT_303:
            case PlafondStatusEnum::REJECT_403:
                $statusId = 2;
                break;
            case PlafondStatusEnum::APPROVED_302:
            case PlafondStatusEnum::APPROVED_402:
                $statusId = 3;
                break;
            case PlafondStatusEnum::DONE_404:
                $statusId = 4;
                break;
            case PlafondStatusEnum::IN_PROGRESS_301:
            case PlafondStatusEnum::IN_PROGRESS_401:
            default:
                $statusId = 1;
                break;
        }

        return [
            'type' => [
                'id' => $dto->type->id,
                'name' => $dto->type->name,
            ],
            'status' => [
                'id' => (string) $statusId,
                'name' => $dto->status->getTranslation(),
            ],
            'updated_at' => unix_timestamp($dto->updatedAt),
            'current_balance' => $dto->currentBalance,
            'added_balance' => $dto->addedBalance,
            'submitted_balance' => $dto->submittedBalance,
            'notes' => $dto->notes,
        ];
    }
}
