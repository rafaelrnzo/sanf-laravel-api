<?php

namespace Sanf\Core\Modules\Scanina\Enums;

use MyCLabs\Enum\Enum;

class ScaninaProductConditionEnum extends Enum
{
    public const NEW = 1;
    public const SECOND = 2;

    public const ALL = [
        self::NEW,
        self::SECOND,
    ];
}
