<?php

namespace Sanf\Api\Modules\Plafond\Transformers;

use League\Fractal\TransformerAbstract;

final class PlafondDisbursementAllocationTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'xid' => $dto->xid,
            'account_name' => $dto->owner,
            'account_provider' => $dto->provider,
            'account_no' => $dto->account_no,
            'notes' => $dto->notes,
            'is_default' => $dto->is_default,
            'amount' => (float) $dto->amount,
            'order_no' => $dto->order_no,
        ];
    }
}
