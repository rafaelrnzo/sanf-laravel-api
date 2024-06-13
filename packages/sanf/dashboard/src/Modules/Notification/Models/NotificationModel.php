<?php

namespace Sanf\Dashboard\Modules\Notification\Models;

use NbsPhp\Core\Models\AbstractModel;

class NotificationModel extends AbstractModel
{
    protected $connection = 'dashboard_db';

    protected $table = 'notification';
    protected $fillable = [
        'xid',
        'notifiable_type',
        'notifiable_id',
        'body',
        'url',
    ];
}
