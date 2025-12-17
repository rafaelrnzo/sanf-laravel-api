<?php

namespace Sanf\Core\Modules\Disbursement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Modules\Disbursement\Enums\SparePartDisbursementStatusEnum;
use Sanf\Dashboard\Modules\User\Models\CustomerBindingEncryptedModel;

/**
 * Spare Part Disbursement.
 *
 * @property int $id
 * @property string $xid
 * @property string $plafond_no
 * @property string $user_auth_xid
 * @property string $supplier_id
 * @property int $disbursement_batch_id
 * @property string $disbursement_batch_xid
 * @property string $batch_number
 * @property ?int $created_by_id
 * @property ?string $customer_id
 * @property ?string $customer_name
 * @property ?string $payment_type
 * @property int $invoice_count
 * @property float $total_invoice_amount
 * @property int $valid_invoice_count
 * @property float $total_valid_invoice_amount
 * @property ?string $validation_status_code
 * @property ?string $validation_status_message
 * @property int $status_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $version
 * @property-read SparePartDisbursementBatchModel $batch
 * @property-read SparePartDisbursementStatusModel $status
 * @property-read \Illuminate\Database\Eloquent\Collection|SparePartDisbursementInvoiceModel[] $invoices
 * @property-read \Illuminate\Database\Eloquent\Collection|SparePartDisbursementInvoiceModel[] $validInvoices
 * @property-read ?CustomerBindingEncryptedModel $supplier
 */
class SparePartDisbursementModel extends Model
{
    use SoftDeletes;

    protected $connection = ConnectionDB::PG_SQL_CMS;
    protected $table = 'spare_part_disbursement';

    protected $fillable = [
        'xid',
        'plafond_no',
        'user_auth_xid',
        'supplier_id',
        'disbursement_batch_id',
        'disbursement_batch_xid',
        'batch_number',
        'created_by_id',
        'customer_id',
        'customer_name',
        'payment_type',
        'invoice_count',
        'total_invoice_amount',
        'valid_invoice_count',
        'total_valid_invoice_amount',
        'validation_status_code',
        'validation_status_message',
        'status_id',
        'version',
    ];

    protected $casts = [
        'invoice_count' => 'integer',
        'total_invoice_amount' => 'decimal:2',
        'valid_invoice_count' => 'integer',
        'total_valid_invoice_amount' => 'decimal:2',
        'status_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(SparePartDisbursementBatchModel::class, 'disbursement_batch_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(SparePartDisbursementStatusModel::class, 'status_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(SparePartDisbursementInvoiceModel::class, 'disbursement_id');
    }

    public function validInvoices(): HasMany
    {
        return $this->hasMany(SparePartDisbursementInvoiceModel::class, 'disbursement_id')
            ->where('status_id', '!=', SparePartDisbursementStatusEnum::REJECTED);
    }

    public function supplier()
    {
        return $this->belongsTo(CustomerBindingEncryptedModel::class, 'supplier_id', 'BowheerId');
    }
}
