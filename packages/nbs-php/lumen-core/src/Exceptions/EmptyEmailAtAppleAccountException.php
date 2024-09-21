<?php

namespace NbsPhp\Core\Exceptions;

class EmptyEmailAtAppleAccountException extends ApiException
{
    protected $code = '400';

    protected $message = 'Failed to Sign In with Apple ID, we need to get your email for this feature, please makesure to unhide your email.';
}
