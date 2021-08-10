<?php

namespace NbsPhp\Core\Exceptions;

class EmailUnverifiedException extends ApiException
{
    protected $code = 'E_AUTH_7';

    protected $message = 'Email Unverified';
}
