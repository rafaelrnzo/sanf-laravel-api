<?php

namespace Sanf\Core\Modules\Payment\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class PaymentCannotBeCancelledException extends ApiException
{
    protected $code = 'E_PAYMENT_NOT_CANCELLABLE';

    protected $message = 'Payment cannot be cancelled or regenerated.';
}
