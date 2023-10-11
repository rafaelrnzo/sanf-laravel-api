<?php


namespace Sanf\Api\Modules\User\Transformers;


use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\User\Enums\ProfileType;

class LoginTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'profile' => [
                'id' => $item->id,
                'xid' => (string)($item->personal_xid ?? $item->xid),
                'full_name' => $item->full_name,
                'email' => $item->username,
                'type_name' => ProfileType::PERSONAL()->getTranslation(),
                'type_id' => ProfileType::PERSONAL(),
                'has_password' => isset($item->password_updated_at) || $item->hasPassword,
                'has_pin' => isset($item->pin_updated_at),
            ]
        ];
    }
}
