<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use League\Fractal\TransformerAbstract;

class FinancingUnitSubmissionContractTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'serial_no' => (string) $item->serial_no,
            'brand_type_model' => (string) $item->brand_type_model,
            'provider_name' => (string) $item->provider_name,
            'year' => (string) $item->year,
            'location_metadata' => fractal($item->location_metadata, ContractFinancingUnitLocationTransformer::class),
            'status' => fractal($item->status, ContractStatusTransformer::class),
            'submitted_location_metadata' => fractal($item->submitted_location_metadata, ContractFinancingUnitLocationTransformer::class),
        ];
    }
}
