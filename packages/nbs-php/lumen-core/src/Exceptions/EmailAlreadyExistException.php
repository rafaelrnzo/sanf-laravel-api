<?php

namespace NbsPhp\Core\Exceptions;

class EmailAlreadyExistException extends ApiException
{
    protected $code = 'E_USR_1';

    protected $message = 'Email Already Exist Exception';
}
