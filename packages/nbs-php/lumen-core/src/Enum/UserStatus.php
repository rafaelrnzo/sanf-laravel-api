<?php

namespace NbsPhp\Core\Enum;

use MyCLabs\Enum\Enum;

/**
 * Class UserStatus.
 */
class UserStatus extends Enum
{
    public const ACTIVE = 10;
    public const SUSPENDED = 20;
    public const NEED_ACTIVATION = 30;
    public const DEACTIVATE = 40;
}
