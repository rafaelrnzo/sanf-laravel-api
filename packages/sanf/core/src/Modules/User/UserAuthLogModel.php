<?php

namespace Sanf\Core\Modules\User;

use NbsPhp\Core\Models\AbstractModel;

class UserAuthLogModel extends AbstractModel
{
    protected $table = 'user_auth_log';

    protected $fillable = [
        'notes',
    ];

    protected $casts = [
        'created_by'
    ];
}
