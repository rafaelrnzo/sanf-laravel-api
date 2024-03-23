<?php

namespace NbsPhp\Notification\Transformers;

use League\Fractal\TransformerAbstract;

class MetadataNotificationTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'unread_count' => (int) ($dto->unread_count ?? 0),
        ];
    }
}
