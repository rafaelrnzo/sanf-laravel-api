<?php

namespace Sanf\Core\Modules\Installment\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class InstallmentNotFoundException extends ApiException
{
    protected $code = 'E_INSTALLMENT_NF';

    protected $message = 'Installment not found';
}
