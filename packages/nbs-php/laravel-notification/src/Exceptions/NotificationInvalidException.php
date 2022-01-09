<?php

namespace NbsPhp\Notification\Exceptions;

class NotificationInvalidException extends NotificationException
{
    protected $code = 'E_NOTIF_1';

    protected $message = 'Invalid Notification';
}
