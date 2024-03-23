<?php

namespace Sanf\Api\Modules\Financing\Transformers;

use League\Fractal\TransformerAbstract;

class GetFinancingCategoryTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'xid' => $item->xid,
            'title' => $item->title,
            'icon_url' => $item->icon_url,
            'image_url' => $item->image_url,
            'description' => $item->description,
            'button_label' => $item->button_label,
            'button_value' => $item->button_value,
            'created_at' => unix_timestamp($item->created_at),
        ];
    }
}
