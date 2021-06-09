<?php

namespace NbsPhp\Core\Exceptions;

class EmailAlreadyExistException extends AppException
{
    protected $code = 'E_USR_1';

    protected $message = 'Email Already Exist Exception';
}
