<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class GenerateSignUrlTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'url' => $item->url,
            'created_at' => unix_timestamp($item->createdAt),
        ];
    }
}