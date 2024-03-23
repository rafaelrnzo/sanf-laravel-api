<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class DetailContractTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'contract_at' => ($item->contract_at) ? Carbon::parse($item->contract_at)->format('Y-m-d') : null,
            'contract_no' => (string) $item->contract_no,
            'currency_type' => (string) $item->currency_type,
            'status' => fractal($item->status, ContractStatusTransformer::class),
            'total_amount' => (string) $item->total_amount,
            'total_invoice_amount' => (string) $item->total_invoice_amount,
            'total_installment_amount' => (string) $item->total_installment_amount,
            'total_installment' => (string) $item->total_installment,
            'total_penalty_amount' => (string) $item->total_penalty_amount,
            'total_paid_amount' => (string) $item->total_paid_amount,
            'total_outstanding_amount' => (string) $item->total_outstanding_amount,
            'due_at' => ($item->due_at) ? Carbon::parse($item->due_at)->format('Y-m-d') : null,
            'installment_count' => (int) $item->installment_count,
            'financing' => fractal($item->financing, DetailFinancingTransformer::class),
            'total_financing_unit' => (int) $item->total_financing_unit,
        ];
    }
}
