<?php


namespace Sanf\Api\Modules\User;


use League\Fractal\TransformerAbstract;

class ProfileTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'id' => $item->id,
            'email' => $item->username,
            'full_name' => $item->full_name,
            'type_id' => 10, //TODO REFACTOR
            'type_name' => 'Pengguna Umum', //TODO REFACTOR
            'is_pic' => true,
            'company_name' => 'PT ANGIN RIBUT'
        ];
    }
}
