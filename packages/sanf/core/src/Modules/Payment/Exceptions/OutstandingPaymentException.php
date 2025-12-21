<?php

namespace Sanf\Core\Modules\Payment\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class OutstandingPaymentException extends ApiException
{
    protected $code = 'E_OUTSTANDING_PAYMENT';

    protected $message = 'Unable to process the payment, there are outstanding payments that have not been paid';
}
