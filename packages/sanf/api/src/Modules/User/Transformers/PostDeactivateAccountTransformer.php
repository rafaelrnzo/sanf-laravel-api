<?php

namespace Sanf\Api\Modules\User\Transformers;

use League\Fractal\TransformerAbstract;

class PostDeactivateAccountTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'restore_expired_at' => unix_timestamp($item->restoreExpiredAt),
        ];
    }
}
