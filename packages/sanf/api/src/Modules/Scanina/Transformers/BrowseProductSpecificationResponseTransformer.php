<?php

namespace Sanf\Api\Modules\Scanina\Transformers;

use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class BrowseProductSpecificationResponseTransformer extends TransformerAbstract
{
    public function transform($dto): array
    {
        return [
            'name' => (string)optional($dto)->name,
            'items' => fractal($dto->specificationColumn, BrowseProductSpecificationItemResponseTransformer::class)
                ->serializeWith(new ArraySerializer()),
        ];
    }
}
