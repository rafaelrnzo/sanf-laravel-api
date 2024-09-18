<?php

namespace Sanf\Core\Modules\Plafond\Models;

use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class PlafondDisbursementSubmissionEncryptedModel extends AbstractModel
{
    use SodiumEncryptionTrait;

    protected $connection = ConnectionDB::PG_SQL;

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
        'revision_notes',
        'user_updated_by',
        'nonce',
    ];

    protected $hidden = [
        'nonce',
    ];

    public function invoicesRelation()
    {
        return $this->hasMany(PlafondDisbursementInvoiceModel::class, 'submission_id', 'id');
    }

    public function allocationsRelation()
    {
        return $this->hasMany(PlafondDisbursementAllocationEncryptedModel::class, 'submission_id', 'id');
    }

    public function documentsRelation()
    {
        return $this->hasMany(PlafondDisbursementDocumentModel::class, 'submission_id', 'id');
    }

    public function getAllocationSnapshotAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['allocation_snapshot']);
    }

    public function getUserUpdatedByAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['user_updated_by']);
    }
}
