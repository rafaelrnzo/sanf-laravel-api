<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use League\Fractal\TransformerAbstract;

class DetailContractTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'contract_at' => (string)$item->contract_at,
            'contract_no' => (string)$item->contract_no,
            'currency_type' => (string)$item->currency_type,
            'status' => fractal($item->status, ContractStatusTransformer::class),
            'total_amount' => (string)$item->total_amount,
            'total_installment' => (int)$item->total_installment,
            'total_outstanding_amount' => (string)$item->total_outstanding_amount,
            'total_paid_amount' => (string)$item->total_paid_amount,
            'total_invoice' => (string)$item->total_invoice,
            'due_at' => (string)$item->due_at,
            'installment_count' => (int)$item->installment_count,
            'financing' => fractal($item->financing, DetailFinancingTransformer::class),
            'total_financing_unit' => (int)$item->total_financing_unit,
        ];
    }
}