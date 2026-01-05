<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class DetailFinancingTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'due_at' => ($item->due_at) ? Carbon::parse($item->due_at)->format('Y-m-d') : null,
            'finished_at' => ($item->finished_at) ? Carbon::parse($item->finished_at)->format('Y-m-d') : null,
            'interest_percentage' => (string) $item->interest_percentage,
            'facility' => fractal($item->facility, FinancingFacilityTransformer::class),
            'method' => fractal($item->method, FinancingMethodTransformer::class),
            'total_tenor' => (int) $item->total_tenor,
            'type' => [
                'id' => $item->type->id,
                'name' => $item->type->name,
            ],
            'plafond_type' => $item->plafond_type,
        ];
    }
}
