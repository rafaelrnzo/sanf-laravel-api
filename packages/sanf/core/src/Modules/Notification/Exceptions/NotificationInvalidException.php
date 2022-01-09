<?php

namespace Sanf\Core\Modules\Notification\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class NotificationInvalidException extends ApiException
{
    protected $code = 'E_NOTIF_1';

    protected $message = 'Invalid Notification';
}
