<?php

namespace Sanf\Core\Modules\Scanina\Enums;

use MyCLabs\Enum\Enum;

class ScaninaProductTypeEnum extends Enum
{
    public const BUY = 1;
    public const RENT = 2;
    public const SPARE_PART = 3;
    public const SERVICE = 4;
}
