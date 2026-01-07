<?php

namespace Sanf\Core\Modules\Payment\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class PaymentNotExpiredException extends ApiException
{
    protected $code = 'E_PAYMENT_NOT_EXP';

    protected $message = 'The payment has not expired yet';
}
