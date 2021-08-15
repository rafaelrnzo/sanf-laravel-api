<?php


namespace Sanf\Api\Modules\User;


use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\User\EntityType;

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
                'type_name' => (new EntityType($item->entity_type_id))->getTranslation(),
                'type_id' => $item->entity_type_id
            ]
        ];
    }
}
