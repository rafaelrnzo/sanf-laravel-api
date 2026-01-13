<?php

namespace Sanf\Core\Modules\Payment\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class PaymentStatusInvalidException extends ApiException
{
    protected $code = 'E_PAYMENT_STATUS';

    protected $message = 'Invalid payment status';
}
