<?php

namespace Sanf\Core\Modules\Notification\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;
use Sanf\Core\Modules\Notification\NotificationTypeEnum;

class SendPushNotificationByExternalRequestDto extends CamelCaseDataTransferObject
{
    public NotificationTypeEnum $type;
    public ?array $email;
    public ?string $screen;
    public string $title;
    public string $subtitle;
    public string $body;
    public int $publishedAt;
    public bool $isNotifyAll;
    public ?array $dashboardWebData; // @since CR2025.
}
