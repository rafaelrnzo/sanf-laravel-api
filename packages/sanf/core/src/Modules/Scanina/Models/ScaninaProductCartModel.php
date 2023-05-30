<?php

namespace Sanf\Core\Modules\Scanina\Models;

use NbsPhp\Core\Models\AbstractModel;

class ScaninaProductCartModel extends AbstractModel
{
    protected $table = 'scanina_product_cart';
    protected $fillable = [
        'xid',
        'profile_xid',
        'type_id',
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
