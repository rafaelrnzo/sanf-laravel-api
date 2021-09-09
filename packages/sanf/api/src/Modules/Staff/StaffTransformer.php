<?php


namespace Sanf\Api\Modules\Staff;


use League\Fractal\TransformerAbstract;

class StaffTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            "no" => $item->no,
            "name" => $item->name,
            "email" => $item->email,
            "status_id" => $item->statusId,
            "status_name" => $item->statusName,
        ];
    }
}
