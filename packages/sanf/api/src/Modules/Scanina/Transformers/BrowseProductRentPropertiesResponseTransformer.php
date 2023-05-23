<?php

namespace Sanf\Api\Modules\Scanina\Transformers;

use League\Fractal\TransformerAbstract;

class BrowseProductRentPropertiesResponseTransformer extends TransformerAbstract
{

    public function transform($dto): array
    {
        return [
            'name' => (string)optional($dto)->label,
            'value' => (string)optional($dto)->value,
        ];
    }
}
