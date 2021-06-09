<?php

namespace NbsPhp\Core\Exceptions;

class InvalidRefreshTokenException extends AppException
{
    protected $code = 'E_AUTH_3';

    public $message = 'Invalid Refresh Token';
}
