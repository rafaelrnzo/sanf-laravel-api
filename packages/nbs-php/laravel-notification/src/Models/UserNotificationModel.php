<?php

namespace NbsPhp\Notification\Models;

use NbsPhp\Core\Models\AbstractModel;

class UserNotificationModel extends AbstractModel
{
    protected $table = 'user_notification';

    protected $dates = [
        'read_at',
    ];

    protected $casts = [
        'data' => 'object',
    ];

    protected $fillable = ['xid', 'type', 'user_id', 'data', 'read_at'];
}
