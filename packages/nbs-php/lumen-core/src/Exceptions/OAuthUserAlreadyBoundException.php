<?php


namespace NbsPhp\Core\Exceptions;


class OAuthUserAlreadyBoundException extends ApiException
{
    protected $code = 'E_OAUTH_3';

    protected $message = 'User Already Registered';
}
