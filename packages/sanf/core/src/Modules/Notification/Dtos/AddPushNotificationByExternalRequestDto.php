<?php

namespace Sanf\Core\Modules\Notification\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;
use Sanf\Core\Modules\Notification\NotificationTypeEnum;

class AddPushNotificationByExternalRequestDto extends CamelCaseDataTransferObject
{
    public string $id;
    public NotificationTypeEnum $type;
    public string $email;
    public ?string $screen;
    public string $customerId;
    public string $title;
    public string $subtitle;
    public string $body;
    public int $publishedAt;
    public ?array $dashboardWebData; // @since CR2025.
}
