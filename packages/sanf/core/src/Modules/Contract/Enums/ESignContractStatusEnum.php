<?php

namespace Sanf\Core\Modules\Contract\Enums;

use MyCLabs\Enum\Enum;

class ESignContractStatusEnum extends Enum
{
    public const SUBMIT = 10;
    public const ON_PROGRESS = 20;
    public const COMPLETED = 30;
}
