<?php

namespace Sanf\Api\Modules\Plafond\Transformers;

use League\Fractal\TransformerAbstract;

class PlafondTypeListTransformer extends TransformerAbstract
{

    public function transform($item)
    {
        return [
            "id" => $item->id,
            "name" => $item->title,
        ];
    }
}
