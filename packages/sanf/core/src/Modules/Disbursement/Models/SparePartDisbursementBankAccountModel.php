<?php

namespace Sanf\Core\Modules\Disbursement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;

/**
 * Spare Part Disbursement Bank Account.
 *
 * @property int $id
 * @property string $xid
 * @property int $disbursement_batch_id
 * @property string $disbursement_batch_xid
 * @property ?string $bank_id
 * @property ?string $bank_account_number (encrypted)
 * @property ?string $bank_provider (encrypted)
 * @property ?string $bank_owner (encrypted)
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $version
 * @property-read SparePartDisbursementBatchModel $batch
 */
class SparePartDisbursementBankAccountModel extends Model
{
    use SodiumEncryptionTrait, SoftDeletes;

    protected $connection = ConnectionDB::PG_SQL_CMS;
    protected $table = 'spare_part_disbursement_bank_account';

    protected $fillable = [
        'xid',
        'disbursement_batch_id',
        'disbursement_batch_xid',
        'bank_id',
        'bank_account_number',
        'bank_provider',
        'bank_owner',
        'version',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'bank_account_number',
        'bank_provider',
        'bank_owner',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(SparePartDisbursementBatchModel::class, 'disbursement_batch_id');
    }

    // Encrypted accessors
    public function getDecryptedBankAccountNumber(): ?string
    {
        if (!$this->bank_account_number) {
            return null;
        }

        return $this->decryptor()->decrypt($this->bank_account_number);
    }

    public function getDecryptedBankProvider(): ?string
    {
        if (!$this->bank_provider) {
            return null;
        }

        return $this->decryptor()->decrypt($this->bank_provider);
    }

    public function getDecryptedBankOwner(): ?string
    {
        if (!$this->bank_owner) {
            return null;
        }

        return $this->decryptor()->decrypt($this->bank_owner);
    }

    // Encrypted mutators
    public function setEncryptedBankAccountNumber(?string $value): void
    {
        $this->bank_account_number = $value ? $this->encryptor()->encrypt($value) : null;
    }

    public function setEncryptedBankProvider(?string $value): void
    {
        $this->bank_provider = $value ? $this->encryptor()->encrypt($value) : null;
    }

    public function setEncryptedBankOwner(?string $value): void
    {
        $this->bank_owner = $value ? $this->encryptor()->encrypt($value) : null;
    }

    public function getMaskedBankAccountNumber(): ?string
    {
        $accountNumber = $this->getDecryptedBankAccountNumber();
        if (!$accountNumber) {
            return null;
        }

        $length = strlen($accountNumber);
        if ($length <= 4) {
            return $accountNumber;
        }

        return str_repeat('*', $length - 4) . substr($accountNumber, -4);
    }
}
