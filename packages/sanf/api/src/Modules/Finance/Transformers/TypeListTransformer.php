<?php


namespace Sanf\Api\Modules\Finance\Transformers;


use League\Fractal\TransformerAbstract;

class TypeListTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            "brand_id" => $dto->brand_id,
            "type_id" => $dto->type_id,
            "type_name" => ucwords(strtolower($dto->type_name)),
        ];
    }
}