<?php

namespace Sanf\Api\Modules\User\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\User\Enums\ProfileType;

class ProfileSimpleTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'xid' => (string) $item->xid,
            'email' => $item->email,
            'full_name' => $item->typeId == ProfileType::PERSONAL ? $item->fullName : $item->picName,
            'type_name' => (new ProfileType($item->typeId))->getTranslation(),
            'type_id' => $item->typeId,
            'is_pic' => $item->isPic,
            'company_name' => $item->fullName,
            'phone_number' => $item->phoneNumber,
            'has_password' => $item->hasPassword,
            'has_pin' => $item->hasPin,
        ];
    }
}
