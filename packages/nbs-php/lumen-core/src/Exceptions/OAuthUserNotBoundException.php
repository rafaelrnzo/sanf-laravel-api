<?php


namespace NbsPhp\Core\Exceptions;


class OAuthUserNotBoundException extends AppException
{
    protected $code = 'E_OAUTH_2';

    protected $message = 'User Not Registered';
}
