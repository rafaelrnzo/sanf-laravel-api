<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use League\Fractal\TransformerAbstract;

class BrowseSubDistrictTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'xid' => $item->xid,
            'name' => $item->name,
            'province_id' => $item->province_id,
            'district_id' => $item->district_id,
            'created_at' => unix_timestamp($item->created_at),
        ];
    }
}
