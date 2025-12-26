<?php

namespace Sanf\Core\Modules\Installment\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class PaidInstallmentException extends ApiException
{
    protected $code = 'E_INSTALLMENT_PAID';

    protected $message = 'The installment have been paid off';
}
