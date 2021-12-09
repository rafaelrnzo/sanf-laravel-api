<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use League\Fractal\TransformerAbstract;

class AccountReceivableContractTransformer extends TransformerAbstract
{

    public function transform($item)
    {
        return [
            'outstanding_amount' => (string)$item->outstanding_amount,
            'paid_amount' => (string)$item->paid_amount,
            'due_date' => (string)$item->due_date,
            'installment' => (string)$item->installment,
            'registration_no' => (string)$item->registration_no,
            'contract_no' => (string)$item->contract_no,
        ];
    }
}