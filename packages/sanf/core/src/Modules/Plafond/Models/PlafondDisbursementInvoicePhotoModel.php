<?php

namespace Sanf\Core\Modules\Plafond\Models;

use NbsPhp\Core\Models\AbstractModel;

class PlafondDisbursementInvoicePhotoModel extends AbstractModel
{
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $table = 'plafond_disbursement_invoice_photo';

    protected $fillable = [
        'xid',
        'plafond_disbursement_id',
        'submission_id',
        'invoice_id',
        'origin_name',
        'file_name',
        'path',
        'metadata',
        'order_no',
        'version',
        'created_at',
        'updated_at',
    ];
}
