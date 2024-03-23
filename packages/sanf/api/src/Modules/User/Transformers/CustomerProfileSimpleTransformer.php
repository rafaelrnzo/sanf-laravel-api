<?php

namespace Sanf\Api\Modules\User\Transformers;

use League\Fractal\TransformerAbstract;

class CustomerProfileSimpleTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'xid' => $item->xid,
            'email' => $item->email,
            'full_name' => $item->fullName,
            'type_id' => $item->typeId,
            'type_name' => $item->typeName,
            'is_pic' => $item->isPic,
            'is_active' => $item->isActive,
        ];
    }
}
