<?php

namespace Sanf\Core\Modules\Contract\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class ESignDocumentHasSignException extends ApiException
{
    protected $code = 'E_ESIGN_2';

    protected $message = 'E-Sign Document Has Been Signed';
}
