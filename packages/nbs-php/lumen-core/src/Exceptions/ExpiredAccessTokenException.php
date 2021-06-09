<?php

namespace NbsPhp\Core\Exceptions;

class ExpiredAccessTokenException extends AppException
{
    protected $code = 'E_AUTH_2';

    protected $message = 'Access Token is Expired';
}
