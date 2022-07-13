<?php

namespace Sanf\Core\Modules\Contract\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class ESignDocumentNotFoundException extends ApiException
{
    protected $code = 'E_ESIGN_3';

    protected $message = 'E-Sign Document Not Found';
}
