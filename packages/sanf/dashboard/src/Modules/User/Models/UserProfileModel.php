<?php

namespace Sanf\Dashboard\Modules\User\Models;

use NbsPhp\Core\Models\AbstractModel;

class UserProfileModel extends AbstractModel
{
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $connection = 'dashboard_db';

    protected $table = 'UserProfile';

    protected $fillable = [
        'xid',
        'userAuthId',
        'fullName',
        'email',
        'createdAt',
        'updatedAt',
    ];
}
