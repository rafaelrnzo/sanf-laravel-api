<?php


namespace Sanf\Api\Modules\User;


use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\User\ProfileType;

class LoginTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'profile' => [
                'id' => $item->id,
                'xid' => $item->xid,
                'full_name' => $item->full_name,
                'email' => $item->username,
                'type_name' => (new ProfileType($item->profile_type))->getTranslation(),
                'type_id' => $item->profile_type
            ]
        ];
    }
}
