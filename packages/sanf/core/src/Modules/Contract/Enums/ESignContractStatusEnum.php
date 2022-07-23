<?php

namespace Sanf\Core\Modules\Contract\Enums;

use MyCLabs\Enum\Enum;

class ESignContractStatusEnum extends Enum
{
    public const SUBMITTED = 10;
    public const ON_PROGRESS = 20;
    public const COMPLETED = 30;
    public const FAILED = 40;
    public const ALL = [self::SUBMITTED, self::ON_PROGRESS, self::COMPLETED];

    public const ASSIGNEE = 10;
    public const DONE = 30;
}
