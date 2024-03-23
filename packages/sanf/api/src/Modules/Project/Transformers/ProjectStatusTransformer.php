<?php

namespace Sanf\Api\Modules\Project\Transformers;

use League\Fractal\TransformerAbstract;

class ProjectStatusTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
        ];
    }
}
