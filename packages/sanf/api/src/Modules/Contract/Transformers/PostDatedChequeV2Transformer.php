<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

/**
 * From SANF Core.
 *
 * @since CR2025
 */
class PostDatedChequeV2Transformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'pdc_no' => (string) $item->pdc_no,
            'contract_no' => (string) $item->contract_no,
            'amount' => (string) $item->amount,
            'currency_type' => (string) $item->currency_type,
            'submitted_date' => ($item->submitted_date) ? Carbon::parse($item->submitted_date)->format('Y-m-d') : null,
            'pdc_type' => (string) $item->pdc_type,
            'status' => fractal($item->status, ContractStatusTransformer::class),
        ];
    }
}
