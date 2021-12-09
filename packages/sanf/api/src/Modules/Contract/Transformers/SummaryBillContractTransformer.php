<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use League\Fractal\TransformerAbstract;

class SummaryBillContractTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'due_at' => (string)$item->due_at,
            'bill_amount' => (string)$item->bill_amount,
            'penalty_amount' => (string)$item->penalty_amount,
            'currency_type' =>  (string)$item->currency_type,
        ];
    }
}