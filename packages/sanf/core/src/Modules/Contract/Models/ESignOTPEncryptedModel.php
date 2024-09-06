<?php

namespace Sanf\Core\Modules\Contract\Models;

use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class ESignOTPEncryptedModel extends AbstractModel
{
    use SodiumEncryptionTrait;

    protected $connection = ConnectionDB::PG_SODIUM;

    protected $table = 'esign_otp_encrypted';

    protected $fillable = [
        'xid',
        'user_id',
        'sanf_id',
        'email',
        'msisdn',
        'expired_at',
        'cooldown_end_at',
        'suspend_end_at',
        'code',
        'reference_no',
        'transaction_no',
        'attempt',
        'created_at',
        'updated_at',
        'nonce',
    ];

    protected $hidden = [
        'nonce',
    ];

    public function getReferenceNoAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['reference_no']);
    }

    public function getEmailAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['email']);
    }

    public function getMsisdnAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['msisdn']);
    }
}
