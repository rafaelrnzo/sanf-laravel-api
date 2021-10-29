<?php


namespace Sanf\Api\Modules\Financing\Transformers;


use League\Fractal\TransformerAbstract;

class ModelListTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            "brand_id" => $dto->brand_id,
            "type_id" => $dto->type_id,
            "model_id" => $dto->model_id,
            "model_name" => ucwords(strtolower($dto->model_name)),
        ];
    }
}