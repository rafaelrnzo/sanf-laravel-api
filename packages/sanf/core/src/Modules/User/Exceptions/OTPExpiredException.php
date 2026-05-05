<?php

namespace Sanf\Core\Modules\User\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class OTPExpiredException extends ApiException
{
    protected $message = 'OTP has expired';
    protected $status = 400;
}
