<?php

namespace Sanf\Api\Modules\Asset;

use League\Fractal\TransformerAbstract;

class PrivateAssetFileSimpleTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'url' => file_get_temp_url($item->path ?? null),
            'file_name' => $item->fileName,
            'origin_name' => $item->originName ?? $item->fileName,
        ];
    }
}
