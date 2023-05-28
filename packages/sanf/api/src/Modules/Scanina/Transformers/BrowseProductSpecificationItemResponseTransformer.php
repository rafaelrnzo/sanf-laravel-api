<?php

namespace Sanf\Api\Modules\Scanina\Transformers;

use League\Fractal\TransformerAbstract;

class BrowseProductSpecificationItemResponseTransformer extends TransformerAbstract
{
    public function transform($dto): array
    {
        $imagesFiles = array_map(function ($files) {
            return $files->path;
        }, $dto->image);

        $videosFiles = array_map(function ($files) {
            return $files->path;
        }, $dto->video);

        return [
            'name' => (string)optional($dto)->name,
            'description' => (string)optional($dto)->value,
            'rating' => (float)optional($dto)->rating,
            'images_file' => (array)optional($dto)->imagesFiles,
            'videos_file' => (array)optional($dto)->videosFiles,
        ];
    }
}
