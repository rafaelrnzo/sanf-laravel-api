<?php

namespace Sanf\Core\Modules\Contract\Enums;

use MyCLabs\Enum\Enum;

class ContractTypeEnum extends Enum
{
    const ACTIVE = 'active';
    const OVERDUE = 'overdue';
    const SETTLED = 'settled';

    const ACTIVE_LABEL = 'AKTIF';
    const SETTLED_LABEL = 'SELESAI';
}
