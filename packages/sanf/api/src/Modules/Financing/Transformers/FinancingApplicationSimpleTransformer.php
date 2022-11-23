<?php


namespace Sanf\Api\Modules\Financing\Transformers;


use League\Fractal\TransformerAbstract;

class FinancingApplicationSimpleTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'xid' => $item->xid,
            'application_code' => $item->application_code,
            'status' => [
                'id' => $item->status_id,
                'name' => $item->status_name,
            ],
            'financing_object_count' => $item->financing_object_count,
            'financing_facility_name' => $item->financing_facility_name,
            'financing_method_name' => $item->financing_method_name,
            'created_at' => unix_timestamp($item->created_at),
        ];
    }
}
