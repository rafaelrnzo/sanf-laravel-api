<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class AccountReceivableContractTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'outstanding_amount' => (string) $item->outstanding_amount,
            'paid_amount' => (string) $item->paid_amount,
            'due_date' => ($item->due_date) ? Carbon::parse($item->due_date)->format('Y-m-d') : null,
            'installment' => (string) $item->installment,
            'registration_no' => (string) $item->registration_no,
            'contract_no' => (string) $item->contract_no,
        ];
    }
}
