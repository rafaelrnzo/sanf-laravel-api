<?php

namespace Sanf\Core\Modules\Payment\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class CustomPaymentUnavailableException extends ApiException
{
    protected $code = 'E_PAYMENT_CUST_UNAVAIL';

    protected $message = 'Custom amount unavailable for bulk payment.';
}
