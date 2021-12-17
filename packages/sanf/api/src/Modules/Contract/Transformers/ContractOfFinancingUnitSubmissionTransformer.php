<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class ContractOfFinancingUnitSubmissionTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'contract_no' => (string)$item->contract_no,
            'created_at' => ($item->created_at) ? Carbon::parse($item->created_at)->unix() : null,
        ];
    }
}