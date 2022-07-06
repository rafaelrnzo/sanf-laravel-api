<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class BrowseProvinceTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'xid' => $item->xid,
            'name' => $item->name,
            'created_at' => unix_timestamp($item->created_at),
        ];
    }
}