<?php

namespace Sanf\Dashboard\Modules\User\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class UserProfileEncryptedModel extends AbstractModel
{
    use SoftDeletes, SodiumEncryptionTrait;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $connection = ConnectionDB::PG_SODIUM_CMS;

    protected $table = 'UserProfileEncrypted';

    public static $snakeAttributes = false;

    protected $fillable = [
        'xid',
        'userAuthId',
        'fullName',
        'email',
        'createdAt',
        'updatedAt',
        'nonce',
    ];

    protected $hidden = [
        'nonce',
    ];

    public function getCreatedByAttribute()
    {
        return json_decode($this->decryptor()->decrypt($this->attributes['createdBy']));
    }

    public function getModifiedByAttribute()
    {
        return json_decode($this->decryptor()->decrypt($this->attributes['modifiedBy']));
    }

    public function getFullNameAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['fullName']);
    }

    public function getEmailAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['email']);
    }

    public function getPhoneNumberAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['phoneNumber']);
    }
}
