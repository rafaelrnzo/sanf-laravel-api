<?php

namespace Sanf\Api\Modules\Project\Transformers;

use League\Fractal\TransformerAbstract;

class ProjectTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'xid' => $item->xid,
            'title' => $item->title,
            'description' => $item->description,
            'image_file' => (object) [
                'url' => file_get_url($item->image_file->path ?? null),
                'file_name' => $item->image_file->file_name,
                'origin_name' => $item->image_file->origin_name ?? $item->image_file->file_name,

            ],
            'location_metadata' => fractal($item->location_metadata, new ProjectLocationTransformer()),
            'phone_number' => $item->phone_number,
            'whatsapp_number' => $item->whatsapp_number,
            'business_email' => $item->business_email,
            'is_owner' => (bool) $item->is_owner,
            'published_at' => (int) unix_timestamp($item->published_at),
            'created_by' => $item->user->full_name,
        ];
    }
}
