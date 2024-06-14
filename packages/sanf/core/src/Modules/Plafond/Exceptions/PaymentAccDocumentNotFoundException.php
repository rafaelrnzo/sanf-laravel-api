<?php

namespace Sanf\Core\Modules\Plafond\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class PaymentAccDocumentNotFoundException extends ApiException
{
    protected $code = 'E_PFACD_1';

    protected $message = 'Harap mengisi form percepatan terlebih dahulu';
}
