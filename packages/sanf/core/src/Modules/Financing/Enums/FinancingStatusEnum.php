<?php

namespace Sanf\Core\Modules\Financing\Enums;

use MyCLabs\Enum\Enum;

class FinancingStatusEnum extends Enum
{
    public const PROCESSED = 10;
    public const ACCEPTED = 20;
    public const REJECTED = 30;
}
