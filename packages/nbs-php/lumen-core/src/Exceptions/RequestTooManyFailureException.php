<?php

namespace NbsPhp\Core\Exceptions;


use Throwable;

class RequestTooManyFailureException extends ApiException
{
    protected $code = '429';

    protected $message = 'Too Many Failure Request, Please Retry After {seconds} Seconds';

    public function __construct($countDown, $message = "", $code = 0, Throwable $previous = null)
    {
        if ($message === "") {
            $message = $this->message;
        }
        $message = strtr($message, ['{seconds}' => $countDown]);
        parent::__construct($message, $code, $previous);
    }
}
