<?php

namespace Sanf\Core\Modules\Plafond\Models;

use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Constants\ConnectionDB;

class PlafondDisbursementInvoiceModel extends AbstractModel
{
    protected $connection = ConnectionDB::PG_SQL;

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $table = 'plafond_disbursement_invoice';

    protected $fillable = [
        'xid',
        'plafond_disbursement_id',
        'submission_id',
        'origin_name',
        'file_name',
        'path',
        'metadata',
        'document_no',
        'document_date',
        'invoice_amount',
        'tax_amount',
        'vat_amount',
        'backharge_amount',
        'other_amount',
        'total_amount',
        'order_no',
        'due_at',
        'version',
        'created_at',
        'updated_at',
    ];

    public function photosRelation()
    {
        return $this->hasMany(PlafondDisbursementInvoicePhotoModel::class, 'invoice_id', 'id');
    }
}
