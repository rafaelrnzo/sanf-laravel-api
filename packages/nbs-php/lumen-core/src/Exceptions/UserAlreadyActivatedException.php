<?php

namespace NbsPhp\Core\Exceptions;

class UserAlreadyActivatedException extends ApiException
{
    protected $code = 'E_AUTH_10';
}
