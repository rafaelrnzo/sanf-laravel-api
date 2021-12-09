<?php

namespace Sanf\Api\Modules\Location\Transformers;

use League\Fractal\TransformerAbstract;

class AllCityListTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
        ];
    }
}