<?php

namespace Sanf\Web\Modules\Common\Transformers;

use League\Fractal\TransformerAbstract;

class SimpleFrequentlyAskQuestionTransformer extends TransformerAbstract
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
