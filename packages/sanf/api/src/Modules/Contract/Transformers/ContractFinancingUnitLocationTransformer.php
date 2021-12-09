<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use League\Fractal\TransformerAbstract;

class ContractFinancingUnitLocationTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'city_id' => (string)$item->city_id,
            'city_name' => (string)$item->city_name,
        ];
    }
}