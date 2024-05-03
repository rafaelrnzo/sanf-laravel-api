<?php

namespace Sanf\Core\Modules\Plafond\Models;

use NbsPhp\Core\Models\AbstractModel;

class PlafondDisbursementAllocationModel extends AbstractModel
{
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $table = 'plafond_disbursement_allocation';

    protected $fillable = [
        'xid',
        'plafond_disbursement_id',
        'submission_id',
        'bank_id',
        'owner',
        'provider',
        'account_no',
        'is_default',
        'amount',
        'notes',
        'order_no',
        'version',
        'created_at',
        'updated_at',
    ];
}
