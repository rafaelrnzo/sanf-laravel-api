<?php

namespace Sanf\Api\Modules\Asset;

use League\Fractal\TransformerAbstract;

class PublicAssetFileSimpleTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'url' => file_get_url($item->path ?? null),
            'file_name' => $item->fileName,
            'origin_name' => $item->originName ?? $item->fileName,
        ];
    }
}
