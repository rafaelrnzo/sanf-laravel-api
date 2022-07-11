<?php

namespace Sanf\Core\Modules\Contract\Enums;

use MyCLabs\Enum\Enum;

class UserRegistrationStatusEnum extends Enum
{
    public const AVAILABLE = 10;
    public const SUBMIT = 20;
    public const COMPLETE = 30;
}
