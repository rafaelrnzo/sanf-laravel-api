<?php

namespace Sanf\Core\Modules\Payment\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class PaymentNoInstallmentException extends ApiException
{
    protected $code = 'E_PAYMENT_NO_INSTALLMENT';

    protected $message = 'There is no installment selected to be paid';
}
