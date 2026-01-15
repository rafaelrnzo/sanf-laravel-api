<?php

namespace Sanf\Core\Modules\Disbursement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;
use Sanf\Dashboard\Modules\User\Models\UserAuthEncryptedModel;

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
 * @property ?string $bank_id
 * @property ?string $bank_account_number (encrypted)
 * @property ?string $bank_provider (encrypted)
 * @property ?string $bank_owner (encrypted)
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $version
 * @property-read \Illuminate\Database\Eloquent\Collection|SparePartDisbursementModel[] $disbursements
 * @property-read \Illuminate\Database\Eloquent\Collection|SparePartDisbursementDocumentModel[] $documents
 * @property-read UserAuthEncryptedModel $createdBy
 */
class SparePartDisbursementBatchModel extends Model
{
    use SoftDeletes, SodiumEncryptionTrait;

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
        'bank_id',
        'bank_account_number',
        'bank_provider',
        'bank_owner',
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

    protected $hidden = [
        'nonce',
    ];

    public function disbursements(): HasMany
    {
        return $this->hasMany(SparePartDisbursementModel::class, 'disbursement_batch_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(SparePartDisbursementDocumentModel::class, 'disbursement_batch_id');
    }

    public function getBankAccountNumberAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['bank_account_number']);
    }

    public function getBankProviderAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['bank_provider']);
    }

    public function getBankOwnerAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['bank_owner']);
    }

    public function createdBy()
    {
        return $this->belongsTo(UserAuthEncryptedModel::class, 'created_by_id');
    }
}
