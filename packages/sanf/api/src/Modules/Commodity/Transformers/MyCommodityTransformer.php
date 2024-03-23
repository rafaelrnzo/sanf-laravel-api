<?php

namespace Sanf\Api\Modules\Commodity\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Api\Modules\Asset\PublicAssetFileSimpleTransformer;

class MyCommodityTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'xid' => $item->xid,
            'title' => $item->title,
            'description' => $item->description,
            'image_file' => fractal($item->image_file, new PublicAssetFileSimpleTransformer()),
            'location_metadata' => fractal($item->location_metadata, new CommodityLocationTransformer()),
            'phone_number' => $item->phone_number,
            'whatsapp_number' => $item->whatsapp_number,
            'business_email' => $item->business_email,
            'status' => fractal($item->status, new CommodityStatusTransformer()),
            'published_at' => (int) unix_timestamp($item->published_at),
            'created_by' => $item->user->full_name,
        ];
    }
}
