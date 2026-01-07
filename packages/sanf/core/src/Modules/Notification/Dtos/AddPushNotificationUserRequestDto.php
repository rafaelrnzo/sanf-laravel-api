<?php

namespace Sanf\Core\Modules\Notification\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class AddPushNotificationUserRequestDto extends DataTransferObject
{
    public int $user_id;
    public AddPushNotificationUserPayloadRequestDto $payload;
    public AddPushNotificationUserDashboardRequestDto $dashboardNotification;
}
