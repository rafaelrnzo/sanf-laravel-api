<?php

namespace NbsPhp\Core\Exceptions;

class InvalidTokenException extends AppException
{
    protected $code = 'E_AUTH_4';

    protected $message = 'Invalid Token';
}
