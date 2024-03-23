<?php

namespace Sanf\Api\Modules\User\Transformers;

use League\Fractal\TransformerAbstract;

class RequestForgotPinTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'code' => (string) $item->reset_pin_code,
            'expired_at' => unix_timestamp($item->reset_pin_expired_at),
        ];
    }
}
