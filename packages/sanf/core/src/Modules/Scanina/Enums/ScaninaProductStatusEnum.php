<?php

namespace Sanf\Core\Modules\Scanina\Enums;

use MyCLabs\Enum\Enum;

class ScaninaProductStatusEnum extends Enum
{
    public const AVAILABLE = 1;
    public const SOLD = 2;

    public const ALL = [
        self::AVAILABLE,
        self::SOLD,
    ];
}
