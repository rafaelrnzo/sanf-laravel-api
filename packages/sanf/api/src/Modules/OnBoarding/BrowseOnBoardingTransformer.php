<?php

namespace Sanf\Api\Modules\OnBoarding;

use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class BrowseOnBoardingTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'id' => $dto->id,
            'title' => Str::title($dto->title),
            'description' => $dto->description,
            'image_url' => ($dto->imageFile) ? file_get_temp_url($dto->imageFile->path) : null,
            'created_at' => unix_timestamp($dto->createdAt),
        ];
    }
}
