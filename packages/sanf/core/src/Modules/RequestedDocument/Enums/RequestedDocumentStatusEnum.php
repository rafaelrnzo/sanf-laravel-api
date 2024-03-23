<?php

namespace Sanf\Core\Modules\RequestedDocument\Enums;

use MyCLabs\Enum\Enum;

class RequestedDocumentStatusEnum extends Enum
{
    const REQUESTED = 10;
    const SUBMITTED = 20;

    const ALL = [
        self::REQUESTED,
        self::SUBMITTED,
    ];
}
