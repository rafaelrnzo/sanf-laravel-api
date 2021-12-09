<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use League\Fractal\TransformerAbstract;

class PostDatedChequeTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'pdc_no' => (string)$item->pdc_no,
            'amount' => (string)$item->amount,
            'currency_type' => (string)$item->currency_type,
            'submitted_date' => (string)$item->submitted_date,
            'pdc_type' => (string)$item->pdc_type,
            'status' => fractal($item->status, ContractStatusTransformer::class),
        ];
    }
}