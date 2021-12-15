<?php

namespace Sanf\Core\Modules\Prepayment\Enums;

use MyCLabs\Enum\Enum;

class PrepaymentStatusEnum extends Enum
{
    public const PROCESSED = 10;
    public const ACCEPTED = 20;
    public const REJECTED = 30;
}
