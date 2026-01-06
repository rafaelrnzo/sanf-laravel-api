<?php

namespace Sanf\Integration\Modules\SanfCore\Enums;

use MyCLabs\Enum\Enum;

class SanfCoreSparePartDisbursementStatusEnum extends Enum
{
    public const WAITING_VALIDATION = '01';
    public const VALID = '05';
    public const NOT_VALID = '06';
}
