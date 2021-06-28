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
                'full_name' => $item->full_name,
                'email' => $item->username,
                'type' => 'Pengguna Umum', //TODO REFACTOR
                'type_id' => EntityType::GENERAL, //TODO REFACTOR
            ]
        ];
    }
}
