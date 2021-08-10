<?php

namespace NbsPhp\Core\Exceptions;

class InvalidCredentialException extends ApiException
{
    protected $code = 'E_AUTH_1';

    protected $message = 'Invalid Credential';
}
