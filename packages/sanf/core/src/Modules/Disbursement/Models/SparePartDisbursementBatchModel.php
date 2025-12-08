<?php

namespace Sanf\Core\Modules\Disbursement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sanf\Core\Constants\ConnectionDB;

/**
 * Spare Part Disbursement Batch.
 *
 * @property int $id
 * @property string $xid
 * @property string $batch_number
 * @property string $user_auth_xid
 * @property ?int $created_by_id
 * @property int $invoice_count
 * @property float $total_invoice_amount
 * @property int $valid_invoice_count
 * @property float $total_valid_invoice_amount
 * @property string $status
 * @property bool $is_validated
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $version
 * @property-read \Illuminate\Database\Eloquent\Collection|SparePartDisbursementModel[] $disbursements
 * @property-read \Illuminate\Database\Eloquent\Collection|SparePartDisbursementDocumentModel[] $documents
 * @property-read ?SparePartDisbursementBankAccountModel $bankAccount
 */
class SparePartDisbursementBatchModel extends Model
{
    use SoftDeletes;

    protected $connection = ConnectionDB::PG_SQL_CMS;
    protected $table = 'spare_part_disbursement_batch';

    protected $fillable = [
        'xid',
        'batch_number',
        'user_auth_xid',
        'created_by_id',
        'invoice_count',
        'total_invoice_amount',
        'valid_invoice_count',
        'total_valid_invoice_amount',
        'status',
        'is_validated',
        'version',
    ];

    protected $casts = [
        'invoice_count' => 'integer',
        'total_invoice_amount' => 'decimal:2',
        'valid_invoice_count' => 'integer',
        'total_valid_invoice_amount' => 'decimal:2',
        'is_validated' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function disbursements(): HasMany
    {
        return $this->hasMany(SparePartDisbursementModel::class, 'disbursement_batch_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(SparePartDisbursementDocumentModel::class, 'disbursement_batch_id');
    }

    public function bankAccount(): HasOne
    {
        return $this->hasOne(SparePartDisbursementBankAccountModel::class, 'disbursement_batch_id');
    }
}
