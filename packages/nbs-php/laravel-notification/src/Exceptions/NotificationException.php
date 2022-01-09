<?php


namespace NbsPhp\Notification\Exceptions;


use NbsPhp\Core\Exceptions\ApiException;

abstract class NotificationException extends ApiException
{
    protected $code;

    protected $message;
}
