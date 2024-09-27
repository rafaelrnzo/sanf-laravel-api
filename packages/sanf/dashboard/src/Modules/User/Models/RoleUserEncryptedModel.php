<?php

namespace Sanf\Dashboard\Modules\User\Models;

use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class RoleUserEncryptedModel extends AbstractModel
{
    use SodiumEncryptionTrait;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $connection = ConnectionDB::PG_SQL_CMS;

    protected $table = 'RoleUser';

    public static $snakeAttributes = false;

    protected $fillable = [
        'userId',
        'roleId',
        'entityTypeId',
        'createdById',
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
}
