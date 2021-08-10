<?php

namespace NbsPhp\Core\Exceptions;

class InvalidRefreshTokenException extends ApiException
{
    protected $code = 'E_AUTH_3';

    public $message = 'Invalid Refresh Token';
}
