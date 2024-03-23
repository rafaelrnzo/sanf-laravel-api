<?php

namespace Sanf\Core\Modules\User\Enums;

use function __;
use MyCLabs\Enum\Enum;

/**
 * Class ProfileType.
 */
class ProfileType extends Enum
{
    const PERSONAL = 'P';
    const COMPANY = 'C';

    public function getTranslation()
    {
        return __('core::constant.profile-type.' . $this->getKey());
    }
}
