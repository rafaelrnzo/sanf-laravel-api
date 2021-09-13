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
            "status_id" => optional(optional($item)->status)->id,
            'status_name' => optional(optional($item)->status)->name,
        ];
    }
}
