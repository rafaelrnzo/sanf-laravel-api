<?php

namespace Sanf\Core\Modules\Payment\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class PaymentInstallmentAlreadySubmittedException extends ApiException
{
    protected $code = 'E_PAYMENT_INSTALL_SUBMIT';

    protected $message = 'Installment of this payment already submitted to core';
}
