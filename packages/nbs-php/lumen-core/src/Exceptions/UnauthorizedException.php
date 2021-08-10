<?php

namespace NbsPhp\Core\Exceptions;

class UnauthorizedException extends ApiException
{
    protected $code = 'E_AUTH_5';

    public $message = 'Unauthorized';
}
