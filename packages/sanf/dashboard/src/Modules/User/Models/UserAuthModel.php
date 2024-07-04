<?php

namespace Sanf\Dashboard\Modules\User\Models;

use NbsPhp\Core\Models\AbstractModel;

class UserAuthModel extends AbstractModel
{
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $connection = 'dashboard_db';

    protected $table = 'UserAuth';

    protected $fillable = [
        'xid',
        'statusId',
        'entityTypeId',
        'username',
        'password',
        'createdAt',
        'updatedAt',
    ];

    public function userProfile()
    {
        return $this->belongsTo(UserProfileModel::class, 'id', 'userAuthId');
    }

    public function userRole()
    {
        return $this->hasOne(RoleUserModel::class, 'userId', 'id');
    }

    public function bindingAccount()
    {
        return $this->belongsTo(CustomerBindingModel::class, 'id', 'userAuthId');
    }

    public function fcmTokens()
    {
        return $this->hasMany(FcmNotificationTokenModel::class, 'userAuthId', 'id');
    }
}
