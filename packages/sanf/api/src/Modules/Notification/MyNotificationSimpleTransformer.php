<?php

namespace Sanf\Api\Modules\Notification;

use League\Fractal\TransformerAbstract;
use function unix_timestamp;

final class MyNotificationSimpleTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'xid' => $dto->xid,
            'title' => $dto->data->title,
            'subtitle' => $dto->data->subtitle,
            'body' => $dto->data->body,
            'type' => (int) $dto->data->type,
            'screen' => $dto->data->screen,
            'read_at' => (int) unix_timestamp($dto->readAt),
            'published_at' => (int) unix_timestamp($dto->data->published_at),
            'created_at' => unix_timestamp($dto->createdAt),
        ];
    }
}
