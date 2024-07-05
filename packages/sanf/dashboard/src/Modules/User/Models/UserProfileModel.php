<?php

namespace Sanf\Dashboard\Modules\User\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use NbsPhp\Core\Models\AbstractModel;

class UserProfileModel extends AbstractModel
{
    use SoftDeletes;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

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
