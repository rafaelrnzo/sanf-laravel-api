<?php

namespace Sanf\Web\Modules\Common\Transformers;

use League\Fractal\TransformerAbstract;

class FaqCategoryTransformer extends TransformerAbstract
{
    public function transform($dto) {
        return [
            'id' => $dto->id,
            'name' => $dto->name,
        ];
    }
}
