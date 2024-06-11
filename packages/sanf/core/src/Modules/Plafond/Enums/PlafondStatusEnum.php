<?php

namespace Sanf\Core\Modules\Plafond\Enums;

use MyCLabs\Enum\Enum;

class PlafondStatusEnum extends Enum
{
    public const IN_PROGRESS_301 = '301';
    public const IN_PROGRESS_401 = '401';
    public const APPROVED_302 = '302';
    public const APPROVED_402 = '402';
    public const REJECT_303 = '303';
    public const REJECT_403 = '403';
    public const DONE_404 = '404';

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
