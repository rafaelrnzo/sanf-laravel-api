<?php

namespace Sanf\Api\Modules\Financing\Transformers;

use League\Fractal\TransformerAbstract;

class FinancingListTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        $transformer = [
            'id' => (int) $item->id,
            'name' => (string) $item->name,
        ];

        if ($item->interest_rate) {
            $transformer['interest_rate'] = (float) number_format($item->interest_rate, 2, '.', '');
        }

        return $transformer;
    }
}
