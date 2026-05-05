<?php

namespace Sanf\Core\Modules\User\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class OTPInvalidException extends ApiException
{
    protected $message = 'Invalid or expired OTP';
    protected $status = 400;
}
