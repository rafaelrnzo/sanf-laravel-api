<?php

namespace Sanf\Core\Modules\Financing\Enums;

use MyCLabs\Enum\Enum;

class FirstInstallmentTypeEnum extends Enum
{
    public const ADDB = 'ADDB';
    public const ADDM = 'ADDM';

    public const ALL = [
        self::ADDB,
        self::ADDM,
    ];
}
