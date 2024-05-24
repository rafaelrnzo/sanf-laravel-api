<?php

namespace Sanf\Api\Modules\Plafond\Transformers;

use League\Fractal\TransformerAbstract;

final class PlafondDisbursementFileMetadataTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'url' => file_get_temp_url($dto->path),
            'file_name' => $dto->file_name,
            'origin_name' => $dto->origin_name,
        ];
    }
}
