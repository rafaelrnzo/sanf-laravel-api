<?php

namespace Sanf\Core\Modules\Contract\Enums;

use MyCLabs\Enum\Enum;

class ESignContractStatusEnum extends Enum
{
    public const SUBMITTED = 10;
    public const ON_PROGRESS = 20;
    public const COMPLETED = 30;
    public const FAILED = 40;
    public const ALL = [self::SUBMITTED, self::ON_PROGRESS, self::COMPLETED, self::FAILED];

    public const ASSIGNEE = 10;
    public const DONE = 30;

    public function toText(): ?string
    {
        $status = [
            self::SUBMITTED => 'Submitted',
            self::ON_PROGRESS => 'On Progress',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
        ];

        return $status[$this->value] ?? null;
    }
}
