<?php

namespace Sanf\Api\Modules\Scanina\Transformers;

use League\Fractal\TransformerAbstract;

class BrowseProductSpecificationItemResponseTransformer extends TransformerAbstract
{
    public function transform($dto): array
    {
        return [
            'name' => (string)optional($dto)->name,
            'description' => (string)optional($dto)->value,
            'rating' => (float)optional($dto)->rating,
            'images_file' => (array)optional($dto)->imagesFile,
        ];
    }
}
