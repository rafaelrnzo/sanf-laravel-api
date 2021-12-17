<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class PostDatedChequeTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'pdc_no' => (string)$item->pdc_no,
            'amount' => (string)$item->amount,
            'currency_type' => (string)$item->currency_type,
            'submitted_date' => ($item->submitted_date) ? Carbon::parse($item->submitted_date)->format('Y-m-d') : null,
            'pdc_type' => (string)$item->pdc_type,
            'status' => fractal($item->status, ContractStatusTransformer::class),
        ];
    }
}