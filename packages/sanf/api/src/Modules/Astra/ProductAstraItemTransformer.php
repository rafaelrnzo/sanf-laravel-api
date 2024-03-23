<?php

namespace Sanf\Api\Modules\Astra;

use League\Fractal\TransformerAbstract;

class ProductAstraItemTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'xid' => $dto->xid,
            'image_url' => $dto->image_url,
            'web_url' => (string) $dto->web_url,
            'android_url' => (string) $dto->android_url,
            'ios_url' => (string) $dto->ios_url,
            'created_at' => unix_timestamp($dto->created_at),
        ];
    }
}
