<?php

namespace Sanf\Api\Modules\News;

use League\Fractal\TransformerAbstract;

class NewsItemTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'xid' => $dto->xid,
            'title' => $dto->title,
            'image_url' => $dto->image_url,
            'link_url' => $dto->link_url,
            'created_at' => unix_timestamp($dto->created_at),
        ];
    }
}