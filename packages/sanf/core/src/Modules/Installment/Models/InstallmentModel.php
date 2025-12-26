<?php

namespace Sanf\Core\Modules\Installment\Models;

use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Modules\Payment\Models\PaymentModel;

/**
 * @property int $id
 * @property string $xid
 * @property int $user_auth_id
 * @property string $user_profile_xid
 * @property string $contract_no
 * @property \Carbon\Carbon $due_date
 * @property float $amount
 * @property int $version
 * @property string|\Sanf\Core\Modules\Installment\Enums\InstallmentStatusEnum $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|PaymentModel[] $payments
 */
class InstallmentModel extends AbstractModel
{
    protected $table = 'installment';

    protected $fillable = [
        'xid',
        'user_auth_id',
        'user_profile_xid',
        'contract_no',
        'due_date',
        'amount',
        'status',
        'version',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'amount' => 'float',
        'version' => 'integer',
    ];

    public function payments()
    {
        return $this->belongsToMany(
            PaymentModel::class,
            'payment_installment',
            'installment_id',
            'payment_id'
        );
    }
}
