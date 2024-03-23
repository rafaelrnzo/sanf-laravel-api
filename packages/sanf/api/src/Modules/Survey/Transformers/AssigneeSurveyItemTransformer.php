<?php

namespace Sanf\Api\Modules\Survey\Transformers;

use League\Fractal\TransformerAbstract;

class AssigneeSurveyItemTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'code' => $item->code,
            'title' => $item->title,
        ];
    }
}
