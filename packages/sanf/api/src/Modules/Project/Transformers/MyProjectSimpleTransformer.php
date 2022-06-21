<?php


namespace Sanf\Api\Modules\Project\Transformers;


use League\Fractal\TransformerAbstract;
use Sanf\Api\Modules\Asset\PublicAssetFileSimpleTransformer;

class MyProjectSimpleTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            "xid" => $item->xid,
            "title" => $item->title,
            "image_file" => fractal($item->image_file, new PublicAssetFileSimpleTransformer()),
            "location_metadata" => fractal($item->location_metadata, new ProjectLocationTransformer()),
            "status" => fractal($item->status, new ProjectStatusTransformer()),
            "published_at" => (int)unix_timestamp($item->published_at),
            "created_at" => (int)unix_timestamp($item->created_at)
        ];
    }
}
