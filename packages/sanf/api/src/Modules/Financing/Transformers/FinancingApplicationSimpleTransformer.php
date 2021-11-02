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
                'id' => $item->status->id,
                'name' => $item->status->name,
            ],
            'financing_object_count' => count($item->objects),
            'financing_facility_name' => $item->facility->name,
            'financing_method_name' => $item->method->name,
            'created_at' => unix_timestamp($item->created_at),
        ];
    }
}
