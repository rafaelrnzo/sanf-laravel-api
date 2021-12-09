<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use League\Fractal\TransformerAbstract;

class DetailFinancingTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'due_at' => (string)$item->due_at,
            'finished_at' => (string)$item->finished_at,
            'interest_percentage' => (string)$item->interest_percentage,
            'facility' => fractal($item->facility, FinancingFacilityTransformer::class),
            'method' => fractal($item->method, FinancingMethodTransformer::class),
        ];
    }
}