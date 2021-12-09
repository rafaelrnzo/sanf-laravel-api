<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use League\Fractal\TransformerAbstract;

class ContractPostDatedChequeTransformer extends TransformerAbstract
{

    public function transform($item)
    {
        return [
            'contract_no' => (string)$item->contract_no,
            'currency_type' => (string)$item->currency_type,
            'created_at' => unix_timestamp($item->created_at),
        ];
    }
}