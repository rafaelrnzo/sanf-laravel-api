<?php

namespace Sanf\Web\Modules\Common\Transformers;

use League\Fractal\TransformerAbstract;

class FaqTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'id' => $dto->id,
            'title' => $dto->title,
            'description' => $dto->description,
        ];
    }
}
