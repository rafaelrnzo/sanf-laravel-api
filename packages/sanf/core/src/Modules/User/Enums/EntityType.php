<?php


namespace Sanf\Core\Modules\User\Enums;


use MyCLabs\Enum\Enum;
use function __;

/**
 * Class EntityType
 * @package Sanf\Core\Modules\User
 */
class EntityType extends Enum
{
    const PERSONAL = 10;
    const COMPANY = 20;

    public function getTranslation()
    {
        return __('core::constant.entity_type.'.$this->getKey());
    }
}
