<?php

namespace Sanf\Core\Modules\Payment\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class UnexistsInstallmentException extends ApiException
{
    protected $code = 'E_UNEXISTS_INSTALLMENT';

    protected $message = 'There is no installment selected to be paid';
}
