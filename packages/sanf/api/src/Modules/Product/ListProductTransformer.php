<?php


namespace Sanf\Api\Modules\Product;


use League\Fractal\TransformerAbstract;

class ListProductTransformer extends TransformerAbstract
{

    public function transform($dto)
    {
        $image = is_string($dto->image) ? json_decode($dto->image) : $dto->image;

        return [
            'id' => $dto->id,
            'title' => $dto->title,
            'description' => $dto->description,
            'image_url' => file_get_url($image->file_name, $image->directory)
        ];
    }
}