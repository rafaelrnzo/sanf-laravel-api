<?php


namespace Sanf\Api\Modules\Finance\Transformers;


use League\Fractal\TransformerAbstract;

class BrandListTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            "brand_id" => $dto->brand_id,
            "brand_name" => ucwords(strtolower($dto->brand_name)),
        ];
    }
}