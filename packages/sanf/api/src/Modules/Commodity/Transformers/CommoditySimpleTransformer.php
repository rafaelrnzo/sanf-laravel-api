<?php

namespace Sanf\Api\Modules\Commodity\Transformers;

use League\Fractal\TransformerAbstract;

class CommoditySimpleTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'xid' => $item->xid,
            'title' => $item->title,
            'image_file' => (object) [
                'url' => file_get_url($item->image_file->path ?? null),
                'file_name' => $item->image_file->file_name,
                'origin_name' => $item->image_file->origin_name ?? $item->image_file->file_name,

            ],
            'location_metadata' => fractal($item->location_metadata, new CommodityLocationTransformer()),
            'is_owner' => (bool) $item->is_owner,
            'published_at' => (int) unix_timestamp($item->published_at),
            'created_by' => $item->modified_by,
        ];
    }
}
