<?php

namespace NbsPhp\Core\Exceptions;

class ExpiredAccessTokenException extends ApiException
{
    protected $code = 'E_AUTH_2';

    protected $message = 'Access Token is Expired';
}
