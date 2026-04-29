<?php

namespace Sanf\Core\Modules\User\Models;

use NbsPhp\Core\Models\AbstractModel;

class RegistrationOTPEncryptedModel extends AbstractModel
{
    protected $table = 'registration_otp_encrypted';

    protected $fillable = [
        'user_id',
        'email',
        'code',
        'purpose',
        'expired_at',
        'cooldown_end_at',
        'suspend_end_at',
        'send_attempt',
        'verify_attempt',
        'is_used',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'cooldown_end_at' => 'datetime',
        'suspend_end_at' => 'datetime',
        'is_used' => 'boolean',
    ];
}
