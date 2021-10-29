<?php


namespace Sanf\Api\Modules\Financing\Transformers;


use League\Fractal\TransformerAbstract;

class FinancingPrerequisiteTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            "id" => (int)$item->id,
            "title" => (string)$item->title,
            "description" => $item->description,
            'items' => $item->items
        ];
    }
}
