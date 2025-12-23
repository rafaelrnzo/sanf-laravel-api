<?php

namespace Sanf\Core\Modules\Disbursement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sanf\Core\Constants\ConnectionDB;

/**
 * Spare Part Disbursement Invoice.
 *
 * @property int $id
 * @property string $xid
 * @property int $disbursement_id
 * @property string $disbursement_xid
 * @property int $disbursement_batch_id
 * @property string $disbursement_batch_xid
 * @property string $customer_id
 * @property string $customer_id_sanfind
 * @property ?string $customer_name
 * @property string $invoice_number
 * @property \Carbon\Carbon $invoice_date
 * @property ?string $invoice_type
 * @property float $invoice_amount
 * @property ?float $sanf_amount
 * @property ?array $detail
 * @property int $status_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $version
 * @property-read SparePartDisbursementModel $disbursement
 * @property-read SparePartDisbursementBatchModel $batch
 * @property-read SparePartDisbursementStatusModel $status
 */
class SparePartDisbursementInvoiceModel extends Model
{
    use SoftDeletes;

    protected $connection = ConnectionDB::PG_SQL_CMS;
    protected $table = 'spare_part_disbursement_invoice';

    protected $fillable = [
        'xid',
        'disbursement_id',
        'disbursement_xid',
        'disbursement_batch_id',
        'disbursement_batch_xid',
        'customer_id',
        'customer_id_sanfind',
        'customer_name',
        'invoice_number',
        'invoice_date',
        'invoice_type',
        'invoice_amount',
        'sanf_amount',
        'detail',
        'status_id',
        'version',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'invoice_amount' => 'decimal:2',
        'sanf_amount' => 'decimal:2',
        'detail' => 'array',
        'status_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function disbursement(): BelongsTo
    {
        return $this->belongsTo(SparePartDisbursementModel::class, 'disbursement_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(SparePartDisbursementBatchModel::class, 'disbursement_batch_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(SparePartDisbursementStatusModel::class, 'status_id');
    }
}
