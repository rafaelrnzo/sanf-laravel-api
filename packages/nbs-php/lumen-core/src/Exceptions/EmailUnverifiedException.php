<?php

namespace NbsPhp\Core\Exceptions;

class EmailUnverifiedException extends AppException
{
    protected $code = 'E_AUTH_7';

    protected $message = 'Email Unverified';
}
