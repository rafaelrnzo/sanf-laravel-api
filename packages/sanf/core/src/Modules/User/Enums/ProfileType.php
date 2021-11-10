<?php


namespace Sanf\Core\Modules\User\Enums;


use MyCLabs\Enum\Enum;
use function __;

/**
 * Class ProfileType
 * @package Sanf\Core\Modules\User
 */
class ProfileType extends Enum
{
    const PERSONAL = 'P';
    const COMPANY = 'C';

    public function getTranslation()
    {
        return __('core::constant.profile-type.'.$this->getKey());
    }
}
