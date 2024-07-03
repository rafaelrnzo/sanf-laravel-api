<?php

namespace Sanf\Dashboard\Modules\User\Models;

use NbsPhp\Core\Models\AbstractModel;

class FcmNotificationTokenModel extends AbstractModel
{
    protected $connection = 'dashboard_db';

    protected $table = 'FcmNotificationToken';
}
