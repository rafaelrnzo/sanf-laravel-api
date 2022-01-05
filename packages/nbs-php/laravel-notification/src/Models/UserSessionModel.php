<?php

namespace NbsPhp\Notification\Models;


use NbsPhp\Core\Models\AbstractModel;

class UserSessionModel extends AbstractModel
{
    protected $table = 'user_session';

    protected $fillable = [
        'id',
        'user_id',
        'auth_provider_id',
        'device_platform_id',
        'device_id',
        'device_manufacturer',
        'device_model',
        'notification_channel_id',
        'notification_token',
        'signature',
        'expired_at',
    ];

    protected $dates = [
        'expired_at'
    ];
}
