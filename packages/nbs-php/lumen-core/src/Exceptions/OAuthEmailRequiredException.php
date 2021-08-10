<?php


namespace NbsPhp\Core\Exceptions;


class OAuthEmailRequiredException extends ApiException
{
    protected $code = 'E_OAUTH_1';

    protected $message = 'Email is Required';
}
