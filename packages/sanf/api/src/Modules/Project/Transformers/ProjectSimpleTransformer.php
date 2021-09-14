<?php


namespace Sanf\Api\Modules\Project\Transformers;


use League\Fractal\TransformerAbstract;
use Sanf\Api\Modules\Asset\AssetSimpleTransformer;

class ProjectSimpleTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            "xid" => $item->xid,
            "title" => $item->title,
            "image_file" => fractal($item->image_file, new AssetSimpleTransformer()),
            "location_metadata" => fractal($item->location_metadata, new ProjectLocationTransformer()),
            "is_owner" => (bool)$item->is_owner,
            "published_at" => (int)$item->published_at,
            "created_by" => $item->modified_by,
        ];
    }
}
