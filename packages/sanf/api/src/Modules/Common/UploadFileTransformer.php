<?php


namespace Sanf\Api\Modules\Common;


use League\Fractal\TransformerAbstract;

class UploadFileTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'origin_name' => $dto->origin_name,
            'file_name' => $dto->file_name,
            'url' => $dto->url
        ];
    }
}