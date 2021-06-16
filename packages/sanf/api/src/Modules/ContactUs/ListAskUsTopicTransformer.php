<?php


namespace Sanf\Api\Modules\ContactUs;


use League\Fractal\TransformerAbstract;

class ListAskUsTopicTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'id' => $dto->id,
            'name' => $dto->name,
        ];
    }
}