<?php

namespace Sanf\Api\Modules\Commodity\Transformers;

use League\Fractal\TransformerAbstract;

class CommodityStatusTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
        ];
    }
}
