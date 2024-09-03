<?php

namespace Sanf\Core\Modules\User;

use Carbon\Carbon;
use NbsPhp\Core\Models\UserOAuthModel;
use NbsPhp\Core\Models\UserStatusModel;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;

/**
 * @property int                $id
 * @property int                $entity_type_id
 * @property string             $username
 * @property string             $password
 * @property string             $remember_token
 * @property string             $full_name
 * @property string             $landline_number
 * @property string             $phone_number
 * @property int                $status_id
 * @property Carbon|string|null $email_verified_at
 * @property Carbon|string|null $last_login_at
 * @property Carbon|string|null $password_updated_at
 * @property Carbon|string|null $created_at
 * @property Carbon|string|null $updated_at
 * @property string             $xid
 * @property string             $profile_type
 * @property resource           $nonce
 */
class AuthEncryptedModel extends AuthModel
{
    use SodiumEncryptionTrait;

    protected $connection = ConnectionDB::PG_SODIUM;

    protected $table = 'user_auth_encrypted';

    protected $fillable = [
        'username',
        'password',
        'full_name',
        'landline_number',
        'phone_number',
        'status_id',
        'password_updated_at',
        'last_login_at',
        'entity_type_id',
        'xid',
        'profile_type',
        'personal_xid',
        'pin',
        'pin_updated_at',
        'reset_pin_code',
        'reset_pin_expired_at',
        'deleted_at',
        'nonce',
    ];

    protected $hidden = [
        'password', 'remember_token', 'pin', 'nonce',
    ];

    public function status()
    {
        return $this->setConnection(ConnectionDB::PG_SQL)->belongsTo(UserStatusModel::class, 'status_id');
    }

    public function oauth()
    {
        return $this->setConnection(ConnectionDB::PG_SQL)->hasOne(UserOAuthModel::class, 'user_id');
    }

    public function deactivateLogs()
    {
        return $this->setConnection(ConnectionDB::PG_SQL)->hasMany(UserAuthLogModel::class, 'user_id', 'id');
    }

    public function getUsernameAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['username']);
    }

    public function getFullNameAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['full_name']);
    }

    public function getLandlineNumberAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['landline_number']);
    }

    public function getPhoneNumberAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['phone_number']);
    }

    public function getCompanyNameAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['company_name']);
    }
}
