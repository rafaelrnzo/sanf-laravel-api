<?php

namespace NbsPhp\Core\Exceptions;

class InvalidCredentialException extends AppException
{
    protected $code = 'E_AUTH_1';

    protected $message = 'Invalid Credential';
}
