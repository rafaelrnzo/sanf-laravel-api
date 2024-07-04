<?php

namespace Sanf\Dashboard\Modules\User\Models;

use NbsPhp\Core\Models\AbstractModel;

class RoleUserModel extends AbstractModel
{
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $connection = 'dashboard_db';

    protected $table = 'RoleUser';

    protected $fillable = [
        'userId',
        'roleId',
        'entityTypeId',
        'createdById',
        'createdAt',
        'updatedAt',
    ];
}
