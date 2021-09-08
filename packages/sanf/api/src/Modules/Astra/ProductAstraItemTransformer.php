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
            'link_url' => $dto->link_url,
            'created_at' => unix_timestamp($dto->created_at),
        ];
    }
}