<?php

namespace Sanf\Dashboard\Modules\Role\Models;

use NbsPhp\Core\Models\AbstractModel;

class RoleModel extends AbstractModel
{
    protected $connection = 'dashboard_db';

    protected $table = 'Role';

    protected $fillable = [
        'userId',
        'roleId',
        'entityTypeId',
        'createdById',
    ];
}
