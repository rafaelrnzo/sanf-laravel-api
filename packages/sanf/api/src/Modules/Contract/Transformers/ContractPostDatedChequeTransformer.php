<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class ContractPostDatedChequeTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'contract_no' => (string) $item->contract_no,
            'currency_type' => (string) $item->currency_type,
            'created_at' => ($item->created_at) ? Carbon::parse($item->created_at)->unix() : null,
        ];
    }
}
