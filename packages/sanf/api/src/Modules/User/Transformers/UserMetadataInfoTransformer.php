<?php

namespace Sanf\Api\Modules\User\Transformers;

use League\Fractal\TransformerAbstract;

class UserMetadataInfoTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'my_project' => [
                'published_count' => $item->projectMetadata->publishedCount,
                'total_count' => $item->projectMetadata->totalCount,
            ],
            'my_commodity' => [
                'published_count' => $item->commodityMetadata->publishedCount,
                'total_count' => $item->commodityMetadata->totalCount,
            ],
        ];
    }
}
