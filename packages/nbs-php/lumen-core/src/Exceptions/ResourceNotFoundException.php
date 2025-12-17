<?php

namespace NbsPhp\Core\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ResourceNotFoundException extends ApiException
{
    // protected $status = Response::HTTP_NOT_FOUND;
    protected $code = 'E_RSC_NOT_FOUND';
    protected $message = 'Resource Not Found';
}
