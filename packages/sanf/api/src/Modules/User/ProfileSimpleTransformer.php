<?php


namespace Sanf\Api\Modules\User;


use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\User\EntityType;

class ProfileSimpleTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'xid' => (string)$item->xid,
            'email' => $item->username,
            'full_name' => $item->full_name,
            'type_id' => $item->entity_type_id,
            'type_name' => (new EntityType($item->entity_type_id))->getTranslation(),
            'is_pic' => optional($item->profile)->isPic,
            'company_name' => optional($item->profile)->companyName
        ];
    }
}
