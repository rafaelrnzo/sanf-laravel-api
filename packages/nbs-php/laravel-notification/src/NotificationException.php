<?php


namespace NbsPhp\Notification;


use NbsPhp\Core\Exceptions\ApiException;

class NotificationException extends ApiException
{
    protected $code;

    protected $message;
}
