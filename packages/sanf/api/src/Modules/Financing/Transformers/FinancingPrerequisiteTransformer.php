<?php

namespace Sanf\Api\Modules\Financing\Transformers;

use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class FinancingPrerequisiteTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'id' => (int) $item->id,
            'title' => (string) $item->title,
            'description' => $item->description,
            'items' => fractal($item->items, new self())->serializeWith(ArraySerializer::class),
        ];
    }
}
