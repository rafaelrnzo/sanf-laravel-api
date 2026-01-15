<?php

namespace Sanf\Core\Modules\Notification\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class AddPushNotificationUserPayloadRequestDto extends DataTransferObject
{
    public string $xid;
    public string $title;
    public string $subtitle;
    public string $body;
    public string $type;
    public ?string $screen;

    /**
     * @var \Illuminate\Support\Carbon|\Carbon\Carbon|string
     */
    public $published_at;

    public ?string $click_action;
    public ?string $link;
}
