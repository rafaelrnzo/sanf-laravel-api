<?php

namespace Sanf\Api\Modules\Shareholder;

use League\Fractal\TransformerAbstract;

class ShareholderTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'no' => $item->no,
            'title' => $item->title,
            'name' => $item->name,
            'share_percentage' => $item->share_percentage,
            'position' => $item->position,
            'type' => $item->type,
        ];
    }
}
