<?php


namespace Sanf\Api\Modules\Commodity\Transformers;


use League\Fractal\TransformerAbstract;
use Sanf\Api\Modules\Asset\AssetFileSimpleTransformer;

class MyCommoditySimpleTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            "xid" => $item->xid,
            "title" => $item->title,
            "image_file" => fractal($item->image_file, new AssetFileSimpleTransformer()),
            "location_metadata" => fractal($item->location_metadata, new CommodityLocationTransformer()),
            "status" => fractal($item->status, new CommodityStatusTransformer()),
            "published_at" => (int)unix_timestamp($item->published_at),
            "created_at" => (int)unix_timestamp($item->created_at)
        ];
    }
}
