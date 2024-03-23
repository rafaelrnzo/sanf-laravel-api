<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class FinancingContractTypeTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'id' => (string) $item->id,
            'name' => Str::title($item->name),
        ];
    }
}
