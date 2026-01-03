<?php

namespace Sanf\Core\Modules\Payment\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Modules\Installment\Models\InstallmentModel;
use Sanf\Core\Modules\Payment\Entities\PaymentDetailEntity;
use Sanf\Core\Modules\Payment\Entities\PaymentStatusLogItemEntity;
use Sanf\Core\Modules\Payment\Entities\PaymentUserSnapshotEntity;
use Sanf\Core\Traits\SodiumEncryptionTrait;

/**
 * @property int $id
 * @property string $xid
 * @property int $user_auth_id
 * @property string $user_profile_xid
 * @property int $disbursement_id
 * @property string $disbursement_xid
 * @property float $amount
 * @property string $currency
 * @property string|\Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum $status
 * @property string|\Sanf\Core\Modules\Payment\Enums\PaymentCategoryEnum $category
 * @property PaymentDetailEntity $payment_detail
 * @property int $version
 * @property \Carbon\Carbon $expired_at
 * @property \Carbon\Carbon|null $paid_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $last_checked_status_at
 * @property-read \Illuminate\Database\Eloquent\Collection|InstallmentModel[] $installments
 * @property ?MidtransTransactionModel $midtransTransaction
 * @property ?MidtransTransactionModel $activeMidtransTransaction
 * @property PaymentStatusLogItemEntity[] $status_log
 * @property PaymentUserSnapshotEntity $user_snapshot
 */
class PaymentModel extends AbstractModel
{
    use SoftDeletes, SodiumEncryptionTrait;

    protected $table = 'payment';

    protected $fillable = [
        'xid',
        'user_auth_id',
        'user_profile_xid',
        'disbursement_id',
        'disbursement_xid',
        'amount',
        'currency',
        'status',
        'category',
        'payment_detail',
        'expired_at',
        'paid_at',
        'status_log',
        'user_snapshot',
        'version',
        'nonce',
        'last_checked_status_at',
    ];

    protected $casts = [
        'amount' => 'float',
        'payment_detail' => 'array',
        'expired_at' => 'datetime',
        'paid_at' => 'datetime',
        'status_log' => 'array',
        'version' => 'integer',
        'last_checked_status_at' => 'datetime',
    ];

    protected $hidden = [
        'nonce',
    ];

    public function getPaymentDetailAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        return new PaymentDetailEntity($value);
    }

    public function getStatusLogAttribute($value)
    {
        return array_map(
            fn ($item) => new PaymentStatusLogItemEntity($item),
            $value ?? []
        );
    }

    public function getUserSnapshotAttribute()
    {
        $snapshot = $this->decryptor()->decrypt($this->attributes['user_snapshot']);

        return new PaymentUserSnapshotEntity(json_decode($snapshot, true));
    }

    public function installments()
    {
        return $this->belongsToMany(
            InstallmentModel::class,
            'payment_installment',
            'payment_id',
            'installment_id'
        )
        ->using(PaymentInstallmentPivot::class)
        ->withPivot(['installment_snapshot'])
        ->withTimestamps();
    }

    public function midtransTransaction()
    {
        return $this->hasOne(MidtransTransactionModel::class, 'payment_id');
    }

    public function activeMidtransTransaction()
    {
        $cancelStatuses = [
            'cancel',
            'canceled',
            'cancelled',
        ];

        return $this->hasOne(MidtransTransactionModel::class, 'payment_id')
            ->where(function ($query) use ($cancelStatuses) {
                $query->whereNull('transaction_status')->orWhereNotIn('transaction_status', $cancelStatuses);
            });
    }
}
