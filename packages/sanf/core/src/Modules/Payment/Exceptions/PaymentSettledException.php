<?php

namespace Sanf\Core\Modules\Payment\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class PaymentSettledException extends ApiException
{
    protected $code = 'E_PAYMENT_SETTLED';

    protected $message = 'The payment already settled';
}
