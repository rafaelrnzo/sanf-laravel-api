<?php

namespace Sanf\Core\Modules\Payment\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class InstallmentWaitingPaymentException extends ApiException
{
    protected $code = 'E_INSTALLMENT_WP';

    protected $message = 'There are some installments need to be paid first';
}
