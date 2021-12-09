<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use League\Fractal\TransformerAbstract;

class ListContractTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'contract_at' => (string)$item->contract_at,
            'contract_no' => (string)$item->contract_no,
            'financing_type' => fractal($item->financing_type, FinancingContractTypeTransformer::class),
            'total_amount' => (string)$item->total_amount,
        ];
    }
}