<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class ContractFinancingUnitLocationTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'city_id' => (string)$item->city_id,
            'city_name' => Str::title($item->city_name),
        ];
    }
}