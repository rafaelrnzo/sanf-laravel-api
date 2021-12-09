<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use League\Fractal\TransformerAbstract;

class ContractStatusTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'id' => (string)$item->id,
            'name' => (string)$item->name
        ];
    }
}