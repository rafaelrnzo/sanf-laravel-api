<?php

namespace NbsPhp\Core\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ConcurrentModificationException extends ApiException
{
    protected $status = Response::HTTP_CONFLICT;
    protected $code = 'E_CONCURRENT_MODIFICATION';
    protected $message = 'Update failed because the data was modified by another process. Please reload and retry.';
}
