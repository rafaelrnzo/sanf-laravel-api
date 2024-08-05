<?php

namespace Sanf\Core\Modules\Financing\Enums;

use MyCLabs\Enum\Enum;

class FirstInstallmentTypeEnum extends Enum
{
    public const AADB = 'AADB';
    public const AADM = 'AADM';

    public const ALL = [
        self::AADB,
        self::AADM,
    ];
}
