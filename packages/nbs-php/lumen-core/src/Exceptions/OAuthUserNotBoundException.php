<?php


namespace NbsPhp\Core\Exceptions;


class OAuthUserNotBoundException extends ApiException
{
    protected $code = 'E_OAUTH_2';

    protected $message = 'User Not Registered';
}
