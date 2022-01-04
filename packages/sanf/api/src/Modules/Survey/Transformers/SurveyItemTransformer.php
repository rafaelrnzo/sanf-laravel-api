<?php

namespace Sanf\Api\Modules\Survey\Transformers;

use League\Fractal\TransformerAbstract;

class SurveyItemTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'code' => $item->code,
            'title' => $item->title,
            'description' => $item->description,
            'image_files' => fractal($item->images, ImageFileTransformer::class),
        ];
    }
}
