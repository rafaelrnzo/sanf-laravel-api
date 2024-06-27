<?php

namespace Sanf\Core\Modules\Plafond\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use NbsPhp\Core\Models\AbstractModel;

class PlafondDisbursementModel extends AbstractModel
{
    use SoftDeletes;

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $table = 'plafond_disbursement';

    protected $fillable = [
        'xid',
        'plafond_id',
        'disbursement_no',
        'client_id',
        'client_name',
        'client_mail',
        'customer_id',
        'customer_bowheer_id',
        'customer_name',
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
        'user_id',
    ];

    public function disbursementRelation()
    {

        return $this->hasOne(PlafondDisbursementSubmissionModel::class, 'xid', 'plafond_submission_xid');
    }
}
