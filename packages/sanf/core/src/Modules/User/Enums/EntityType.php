<?php

namespace Sanf\Core\Modules\User\Enums;

use function __;
use MyCLabs\Enum\Enum;

/**
 * Class EntityType.
 */
class EntityType extends Enum
{
    const PERSONAL = 10;
    const COMPANY = 20;

    public function getTranslation()
    {
        return __('core::constant.entity_type.' . $this->getKey());
    }
}
