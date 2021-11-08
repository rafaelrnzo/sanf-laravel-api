<?php


namespace Sanf\Api\Modules\Financing\Transformers;


use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class FinancingApplicationTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'xid' => $item->xid,
            'application_code' => $item->application_code,
            'registration_code' => $item->registration_code,
            'status' => [
                'id' => $item->status->id,
                'name' => $item->status->name,
            ],
            'financing_object_count' => count($item->objects),
            'financing_facility_name' => optional($item->facility)->name,
            'financing_method_name' => optional($item->method)->name,
            'financing_objects' => fractal($item->objects, new FinancingObjectTransformer())
                ->serializeWith(ArraySerializer::class),
            'created_at' => unix_timestamp($item->created_at),
        ];
    }
}
