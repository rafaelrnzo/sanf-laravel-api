<?php

namespace Sanf\Api\Modules\Promo;

use League\Fractal\TransformerAbstract;

class PromoItemTransformer extends TransformerAbstract
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
