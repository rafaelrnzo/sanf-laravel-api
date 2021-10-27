<?php


namespace Sanf\Api\Modules\Financing\Transformers;

use League\Fractal\TransformerAbstract;

class FinancingListTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            "id" => (int)$item->id,
            "name" => (string)$item->name,
        ];
    }

}
