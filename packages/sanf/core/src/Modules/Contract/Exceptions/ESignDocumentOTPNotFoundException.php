<?php

namespace Sanf\Core\Modules\Contract\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class ESignDocumentOTPNotFoundException extends ApiException
{
    protected $code = 'E_OTP_2';

    protected $message = 'Request OTP not found';
}
