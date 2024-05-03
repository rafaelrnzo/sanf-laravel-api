<?php

namespace Sanf\Core\Modules\Plafond\Enums;

use MyCLabs\Enum\Enum;

class PlafondStatusEnum extends Enum
{
    public const IN_PROGRESS = '101';
    public const APPROVED = '102';
    public const REJECT = '103';
    public const SUBMIT = '01';
    public const ON_REVIEW = '02';
    public const REVISION = '03';
    public const PROCESS = '04';
    public const TRANSFERED = '05';

    public function getTranslation()
    {
        return __('core::constant.plafond-status.' . $this->getKey());
    }
}
