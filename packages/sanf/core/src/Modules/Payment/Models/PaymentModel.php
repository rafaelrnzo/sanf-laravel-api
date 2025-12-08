<?php

namespace Sanf\Core\Modules\Payment\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Modules\Installment\Models\InstallmentModel;

/**
 * @property int $id
 * @property string $xid
 * @property int $user_auth_id
 * @property int $disbursement_id
 * @property string $disbursement_xid
 * @property float $amount
 * @property string $currency
 * @property string|\Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum $status
 * @property array $payment_detail
 * @property \Carbon\Carbon $due_date
 * @property \Carbon\Carbon|null $paid_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|InstallmentModel[] $installments
 * @property-read MidtransTransactionModel|null $midtransTransaction
 */
class PaymentModel extends AbstractModel
{
    use SoftDeletes;

    protected $table = 'payment';

    protected $fillable = [
        'xid',
        'user_auth_id',
        'disbursement_id',
        'disbursement_xid',
        'amount',
        'currency',
        'status',
        'payment_detail',
        'due_date',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'float',
        'payment_detail' => 'array',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function installments()
    {
        return $this->belongsToMany(
            InstallmentModel::class,
            'payment_installment',
            'payment_id',
            'installment_id'
        );
    }

    public function midtransTransaction()
    {
        return $this->hasOne(MidtransTransactionModel::class, 'payment_id');
    }
}
