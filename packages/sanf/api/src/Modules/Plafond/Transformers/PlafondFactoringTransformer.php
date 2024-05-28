<?php

namespace Sanf\Api\Modules\Plafond\Transformers;

use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

final class PlafondFactoringTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'xid' => $dto->xid,
            'submit_balance' => $dto->submitAmount,
            'remaining_balance' => $dto->remainingAmount,
            'used_balance' => $dto->usedAmount,
            'customer_review' => $dto->customerReview,
            'customers' => fractal($dto->customers)
                ->transformWith(CustomerPlafondFactoringTransformer::class)
                ->serializeWith(new ArraySerializer()),
            'expired_at' => unix_timestamp($dto->expiredAt->endOfDay()),
        ];
    }
}
