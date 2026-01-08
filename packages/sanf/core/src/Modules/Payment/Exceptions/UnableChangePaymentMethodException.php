<?php

namespace Sanf\Core\Modules\Payment\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class UnableChangePaymentMethodException extends ApiException
{
    protected $code = 'E_PAYMENT_CHANGE';

    protected $message = 'Unable to change payment method.';
}
