<?php

namespace Sanf\Core\Modules\Notification\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class AddPushNotificationPartnerRequestDto extends DataTransferObject
{
    public string $bowheerId;
    public AddPushNotificationUserPayloadRequestDto $payload;
    public AddPushNotificationUserDashboardRequestDto $dashboardNotification;
}
