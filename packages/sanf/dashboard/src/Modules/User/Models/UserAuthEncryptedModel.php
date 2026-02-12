<?php

namespace Sanf\Dashboard\Modules\User\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class UserAuthEncryptedModel extends AbstractModel
{
    use SoftDeletes, SodiumEncryptionTrait;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $connection = ConnectionDB::PG_SODIUM_CMS;

    protected $table = 'UserAuthEncrypted';

    public static $snakeAttributes = false;

    protected $fillable = [
        'xid',
        'statusId',
        'entityTypeId',
        'username',
        'password',
        'createdAt',
        'updatedAt',
        'nonce',
        'activatedAt',
        'lastLoginAt',
        'deletedAt',
        'fullName',
        'avatarFile',
        'createdBy',
        'modifiedBy',
        'version',
        'metadata',
        'forgotPasswordAt',
        'resendTokenAt',
        'suspendedAt',
        'remember_token',
        'api_user_auth_id',
        'passwordHistory',
    ];

    protected $hidden = [
        'nonce',
    ];

    public function userProfile()
    {
        return $this->belongsTo(UserProfileEncryptedModel::class, 'id', 'userAuthId');
    }

    public function userRole()
    {
        return $this->hasOne(RoleUserEncryptedModel::class, 'userId', 'id');
    }

    public function bindingAccount()
    {
        return $this->belongsTo(CustomerBindingEncryptedModel::class, 'id', 'userAuthId');
    }

    public function fcmTokens()
    {
        return $this->hasMany(FcmNotificationTokenModel::class, 'userAuthId', 'id');
    }

    public function getCreatedByAttribute()
    {
        return  json_decode((string) $this->decryptor()->decrypt($this->attributes['createdBy']));
    }

    public function getModifiedByAttribute()
    {
        return  json_decode((string) $this->decryptor()->decrypt($this->attributes['modifiedBy']));
    }

    public function getUsernameAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['username']);
    }

    public function getFullNameAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['fullName']);
    }
}
