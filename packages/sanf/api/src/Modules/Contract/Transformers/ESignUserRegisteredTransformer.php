<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use League\Fractal\TransformerAbstract;

class ESignUserRegisteredTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'email' => $item->email,
            'response_code' => $item->response_code,
        ];
    }
}
