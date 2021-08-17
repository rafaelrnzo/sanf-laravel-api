<?php


namespace Sanf\Api\Modules\User;


use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\User\ProfileType;

class ProfileSimpleTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'xid' => (string)$item->xid,
            'email' => $item->username,
            'full_name' => $item->full_name,
            'type_name' => (new ProfileType($item->profile_type))->getTranslation(),
            'type_id' => $item->profile_type,
            'is_pic' => optional($item->profile)->isPic,
            'company_name' => optional($item->profile)->companyName,
            'phone_number' => optional($item->profile)->phoneNumber
        ];
    }
}
