<?php

namespace Sanf\Api\Modules\Financing\Transformers;

use League\Fractal\TransformerAbstract;

class FinancingApplicationSimpleTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'xid' => (string) $item->xid,
            'application_code' => (string) $item->application_code,
            'status' => [
                'id' => (int) $item->status_id,
                'name' => (string) $item->status_name,
            ],
            'financing_object_count' => (int) $item->financing_object_count,
            'financing_facility_name' => (string) $item->financing_facility_name,
            'financing_method_name' => (string) $item->financing_method_name,
            'created_at' => unix_timestamp($item->created_at),
        ];
    }
}
