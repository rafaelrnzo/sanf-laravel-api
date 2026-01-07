<?php

namespace Sanf\Core\Modules\Notification\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class AddPushNotificationUserDashboardRequestDto extends DataTransferObject
{
    public string $xid;
    public string $notifiable_type;
    public string $notifiable_id;
    public string $body;
    public string $url;
    public string $title;
}
