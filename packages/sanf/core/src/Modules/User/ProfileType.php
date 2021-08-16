<?php


namespace Sanf\Core\Modules\User;


use MyCLabs\Enum\Enum;

/**
 * Class EntityType
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
