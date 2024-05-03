<?php

namespace Sanf\Core\Modules\Plafond\Models;

use NbsPhp\Core\Models\AbstractModel;

class PlafondDisbursementModel extends AbstractModel
{
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $table = 'plafond_disbursement';

    protected $fillable = [
        'xid',
        'client_id',
        'client_name',
        'client_mail',
        'customer_id',
        'customer_mail',
        'customer_code',
        'customer_review',
        'status_id',
        'status',
        'client_amount',
        'customer_amount',
        'admin_amount',
        'plafond_submission_xid',
        'customer_updated_at',
        'admin_updated_at',
        'version',
        'created_at',
        'updated_at',
    ];
}
