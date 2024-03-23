<?php

namespace Sanf\Api\Modules\User\Transformers;

use League\Fractal\TransformerAbstract;

class UserMetadataAccountReceivableTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'total_outstanding_amount' => (string) $item->total_outstanding_amount,
            'total_paid_amount' => (string) $item->total_paid_amount,
            'currency_type' => (string) $item->currency_type,
        ];
    }
}
