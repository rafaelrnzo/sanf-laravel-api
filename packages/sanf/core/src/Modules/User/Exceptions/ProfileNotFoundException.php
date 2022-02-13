<?php

namespace Sanf\Core\Modules\User\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class ProfileNotFoundException extends ApiException
{
    protected $code = 'E_USR_3';

    protected $message = 'Profile Not Found';
}
