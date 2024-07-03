<?php

namespace Sanf\Dashboard\Modules\User\Models;

use NbsPhp\Core\Models\AbstractModel;

class UserAuthModel extends AbstractModel
{
    protected $connection = 'dashboard_db';

    protected $table = 'UserAuth';

    public function bindingAccount()
    {
        return $this->belongsTo(CustomerBindingModel::class, 'id', 'userAuthId');
    }

    public function fcmTokens()
    {
        return $this->hasMany(FcmNotificationTokenModel::class, 'userAuthId', 'id');
    }
}
