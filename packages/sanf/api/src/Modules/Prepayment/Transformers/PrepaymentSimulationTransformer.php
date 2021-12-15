<?php

namespace Sanf\Api\Modules\Prepayment\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

final class PrepaymentSimulationTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'contract_no' => $dto->contractNo,
            'prepayment_date' => Carbon::createFromImmutable($dto->prepaymentDate)->format('Y-m-d'),
            'total_prepayment' => $dto->totalPrepayment,
            'currency_type' => $dto->currencyType,
            'items' => fractal($dto->items, PrepaymentSimulationItemTransformer::class),
        ];
    }
}
