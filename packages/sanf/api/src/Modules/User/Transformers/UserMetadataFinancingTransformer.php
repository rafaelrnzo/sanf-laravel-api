<?php

namespace Sanf\Api\Modules\User\Transformers;

use League\Fractal\TransformerAbstract;

class UserMetadataFinancingTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'application_unread_count' => $item->unread_count ?? 0,
        ];
    }
}
