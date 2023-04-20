<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class ListContractTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        $response = [
            'contract_at' => ($item->contract_at) ? Carbon::parse($item->contract_at)->format('Y-m-d') : null,
            'contract_no' => (string)$item->contract_no,
            'financing_type' => fractal($item->financing_type, FinancingContractTypeTransformer::class),
            'total_amount' => (string)$item->total_amount,
            'currency_type' => (string)$item->currency_type,
        ];

        if (!is_null($item->payment_due_at)) {
            $response += [
                'payment_due_at' => $item->payment_due_at,
            ];
        }

        if (!is_null($item->days)) {
            $response += [
                'days' => $item->days,
            ];
        }

        return $response;
    }
}