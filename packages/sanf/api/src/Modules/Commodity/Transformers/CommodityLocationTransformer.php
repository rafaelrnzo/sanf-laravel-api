<?php


namespace Sanf\Api\Modules\Commodity\Transformers;


use League\Fractal\TransformerAbstract;

class CommodityLocationTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            "province_id" => $item->province_id,
            "province_name" => $item->province_name,
            "city_id" => $item->city_id,
            "city_name" => $item->city_name,
        ];
    }
}
