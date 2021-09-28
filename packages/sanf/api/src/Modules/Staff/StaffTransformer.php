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
            "email" => empty(trim($item->email)) ? null : $item->email,
            "status_id" => (int)optional(optional($item)->status)->id,
            'status_name' => (string)optional(optional($item)->status)->name,
        ];
    }
}
