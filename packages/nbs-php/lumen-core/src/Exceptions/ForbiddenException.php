<?php

namespace NbsPhp\Core\Exceptions;

class ForbiddenException extends ApiException
{
    protected $code = '403';
    protected $status = 403;
}
