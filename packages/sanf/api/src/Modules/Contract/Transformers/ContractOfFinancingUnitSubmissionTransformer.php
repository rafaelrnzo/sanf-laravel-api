<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use League\Fractal\TransformerAbstract;

class ContractOfFinancingUnitSubmissionTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'contract_no' => (string)$item->contract_no,
            'created_at' => unix_timestamp($item->created_at),
        ];
    }
}