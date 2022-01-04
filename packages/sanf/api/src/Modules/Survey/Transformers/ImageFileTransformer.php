<?php

namespace Sanf\Api\Modules\Survey\Transformers;

use League\Fractal\TransformerAbstract;

class ImageFileTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'file_name' => $item->file_name,
            'origin_name' => $item->origin_name,
            'url' => $item->url,
        ];
    }
}
