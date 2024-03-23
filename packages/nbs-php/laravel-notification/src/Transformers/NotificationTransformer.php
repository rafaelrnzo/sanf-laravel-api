<?php

namespace NbsPhp\Notification\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class NotificationTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'xid' => $item->xid,
            'type' => $item->type,
            'data' => $item->data,
            'read_at' => optional(Carbon::make($item->read_at))->timestamp,
            'created_at' => (int) Carbon::make($item->created_at)->timestamp,
        ];
    }
}
