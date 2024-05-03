<?php

namespace Sanf\Core\Modules\Plafond\Models;

use NbsPhp\Core\Models\AbstractModel;

class PlafondDisbursementSubmissionModel extends AbstractModel
{
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $table = 'plafond_disbursement_submission';

    protected $fillable = [
        'xid',
        'plafond_disbursement_id',
        'client_amount',
        'customer_amount',
        'invoice_snapshot',
        'allocation_snapshot',
        'other_doc_snapshot',
        'payment_acc_doc_origin_name',
        'payment_acc_doc_file_name',
        'payment_acc_doc_path',
        'payment_acc_doc_metadata',
        'status_id',
        'status',
        'version',
        'created_at',
        'updated_at',
    ];
}
