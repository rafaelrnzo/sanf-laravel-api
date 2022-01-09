<?php

namespace Sanf\Api\Modules\Survey\Transformers;

use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class SurveyItemTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'code' => $item->code,
            'title' => $item->title,
            'description' => $item->description,
            'image_files' => fractal($item->image_files, ImageFileTransformer::class)
                ->serializeWith(new ArraySerializer()),
        ];
    }
}
