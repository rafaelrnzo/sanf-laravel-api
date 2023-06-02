<?php

namespace Sanf\Core\Modules\Scanina\Models;

use NbsPhp\Core\Models\AbstractModel;

class ScaninaUserRegistrationModel extends AbstractModel
{
    protected $table = 'scanina_user_registration';
    protected $fillable = [
        'xid',
        'profile_xid',
        'email',
        'snapshot_request_body',
        'snapshot_response_body',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'snapshot_request_body' => 'object',
        'snapshot_response_body' => 'object',
    ];
}
