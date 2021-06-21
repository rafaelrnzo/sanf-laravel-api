<?php


namespace NbsPhp\Core\Exceptions;


class OAuthUserAlreadyBoundException extends AppException
{
    protected $code = 'E_OAUTH_3';

    protected $message = 'User Already Registered';
}
