<?php


namespace Sanf\Api\Modules\Financing\Transformers;


use League\Fractal\TransformerAbstract;

class FinancingObjectTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'amount' => $dto->amount,
            'provider_name' => $dto->provider_name,
            'brand_name' => $dto->brand_name,
            'type_name' => $dto->type_name,
            'model_name' => $dto->model_name,
            'created_at' => unix_timestamp($dto->created_at),
        ];
    }
}
