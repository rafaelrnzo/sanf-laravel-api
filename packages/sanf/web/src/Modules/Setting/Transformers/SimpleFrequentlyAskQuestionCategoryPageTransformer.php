<?php

namespace Sanf\Web\Modules\Setting\Transformers;

use League\Fractal\TransformerAbstract;

class SimpleFrequentlyAskQuestionCategoryPageTransformer extends TransformerAbstract
{
    public function transform($dto) {
        return [
            'id' => $dto->id,
            'name' => $dto->name,
        ];
    }
}
