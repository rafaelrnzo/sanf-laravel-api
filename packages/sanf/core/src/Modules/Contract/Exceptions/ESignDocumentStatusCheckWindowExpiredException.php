<?php

namespace Sanf\Core\Modules\Contract\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;
use Symfony\Component\HttpFoundation\Response;

class ESignDocumentStatusCheckWindowExpiredException extends ApiException
{
    protected $status = Response::HTTP_TOO_MANY_REQUESTS;

    protected $code = 'E_ESIGN_7';

    protected $message = 'Status can only be checked within 30 minutes after signed';
}
