<?php

namespace Sanf\Api\Modules\PdcHold\Transformers;

use Illuminate\Support\Carbon;
use League\Fractal\TransformerAbstract;

/**
 * @since CR2025
 */
final class PdcHoldGiroSubmissionTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'xid' => $dto->xid,
            'contract_no' => $dto->contract_no,
            'pdc_no' => $dto->pdc_no,
            'amount' => $dto->amount,
            'currency_type' => $dto->currency_type,
            'giro_date' => Carbon::make($dto->giro_date)->format('Y-m-d'),
            'pdc_type' => $dto->pdc_type,
            'created_at' => unix_timestamp($dto->created_at),
            'updated_at' => unix_timestamp($dto->updated_at),
        ];
    }
}
