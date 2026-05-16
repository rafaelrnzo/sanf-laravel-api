<?php

namespace Sanf\Core\Modules\User\Models;

use Sanf\Core\Constants\ConnectionDB;
use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Traits\SodiumEncryptionTrait;


class RegistrationOTPEncryptedModel extends AbstractModel
{
    use SodiumEncryptionTrait;

    protected $connection = ConnectionDB::PG_SODIUM;

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
        'nonce',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'cooldown_end_at' => 'datetime',
        'suspend_end_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    public function getEmailAttribute()
    {
        if (is_null($this->attributes['email'])) {
            return null;
        }
        
        return $this->decryptor()->decrypt($this->attributes['email']);
    }
}
