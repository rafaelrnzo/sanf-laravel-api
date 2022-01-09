<?php

namespace Sanf\Core\Modules\Notification;

use MyCLabs\Enum\Enum;

class NotificationTypeEnum extends Enum
{
    public const INFO = 1;
    public const REMINDER = 2;

    public const ALL_TYPE = [self::INFO, self::REMINDER];
}
