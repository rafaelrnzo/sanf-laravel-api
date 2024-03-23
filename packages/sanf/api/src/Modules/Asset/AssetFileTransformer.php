<?php

namespace Sanf\Api\Modules\Asset;

use League\Fractal\TransformerAbstract;

class AssetFileTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'url' => $item->url,
            'origin_name' => $item->origin_name ?? $item->file_name,
            'file_name' => $item->file_name,
            'file_size' => $item->file_size,
            'file_type' => $item->file_type,
            'mime_type' => $item->mime_type,
            'asset_type_id' => $item->asset_type_id,
        ];
    }
}
