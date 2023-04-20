<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use League\Fractal\TransformerAbstract;

class ListItemRequestedDocumentTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'id' => $dto->id,
            'title' => $dto->title,
            'is_uploaded' => $dto->is_uploaded,
        ];
    }
}