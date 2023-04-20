<?php

namespace Sanf\Core\Modules\Contract\Enums;

use MyCLabs\Enum\Enum;


class DocumentTypeEnum extends Enum
{
    const CONTRACT = 10;
    const SUBMISSION = 20;
    const PERSONAL = 30;

    const ALL = [
        self::CONTRACT,
        self::SUBMISSION,
        self::PERSONAL,
    ];
}