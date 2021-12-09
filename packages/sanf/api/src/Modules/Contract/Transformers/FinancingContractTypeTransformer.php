<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use League\Fractal\TransformerAbstract;

class FinancingContractTypeTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'id' => (string)$item->id,
            'name' => (string)$item->name,
        ];
    }
}