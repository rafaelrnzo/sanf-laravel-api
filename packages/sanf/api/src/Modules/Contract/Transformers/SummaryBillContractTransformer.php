<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class SummaryBillContractTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'due_at' => ($item->due_at) ? Carbon::parse($item->due_at)->format('Y-m-d') : null,
            'bill_amount' => (string) $item->bill_amount,
            'penalty_amount' => (string) $item->penalty_amount,
            'currency_type' =>  (string) $item->currency_type,
            'installment_index' => (string) $item->installment_index,
        ];
    }
}
