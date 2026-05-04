<?php

namespace Sanf\Core\Modules\User\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class OTPPurposeInvalidException extends ApiException
{
    protected $message = 'Invalid OTP purpose';
    protected $status = 400;
}
