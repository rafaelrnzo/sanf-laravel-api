<?php

namespace Sanf\Dashboard\Modules\Role\Models;

use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class RoleEncryptedModel extends AbstractModel
{
    use SodiumEncryptionTrait;

    protected $connection = ConnectionDB::PG_SQL_CMS;

    protected $table = 'Role';

    public static $snakeAttributes = false;

    protected $fillable = [
        'userId',
        'roleId',
        'entityTypeId',
        'createdById',
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
