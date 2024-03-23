<?php

namespace Sanf\Api\Modules\Project\Transformers;

use League\Fractal\TransformerAbstract;

class ProjectLocationTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'province_id' => $item->province_id,
            'province_name' => $item->province_name,
            'city_id' => $item->city_id,
            'city_name' => $item->city_name,
        ];
    }
}
