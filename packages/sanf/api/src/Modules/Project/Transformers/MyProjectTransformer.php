<?php

namespace Sanf\Api\Modules\Project\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Api\Modules\Asset\PublicAssetFileSimpleTransformer;

class MyProjectTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'xid' => $item->xid,
            'title' => $item->title,
            'submission_limit_at' => unix_timestamp($item->submission_limit_at),
            'description' => $item->description,
            'image_file' => fractal($item->image_file, new PublicAssetFileSimpleTransformer()),
            'location_metadata' => fractal($item->location_metadata, new ProjectLocationTransformer()),
            'phone_number' => $item->phone_number,
            'whatsapp_number' => $item->whatsapp_number,
            'business_email' => $item->business_email,
            'status' => fractal($item->status, new ProjectStatusTransformer()),
            'published_at' => (int) unix_timestamp($item->published_at),
            'created_by' => $item->user->full_name,
        ];
    }
}
