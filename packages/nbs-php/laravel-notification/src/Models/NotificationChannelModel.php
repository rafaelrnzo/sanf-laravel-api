<?php

namespace NbsPhp\Notification\Models;

use NbsPhp\Core\Models\AbstractModel;

class NotificationChannelModel extends AbstractModel
{
    protected $table = 'notification_channel';

    public function userSessions()
    {
        return $this->hasMany(UserSessionModel::class);
    }
}
