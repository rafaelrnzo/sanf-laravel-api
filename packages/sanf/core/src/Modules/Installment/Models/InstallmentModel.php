<?php

namespace Sanf\Core\Modules\Installment\Models;

use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Modules\Payment\Models\PaymentModel;

/**
 * @property int $id
 * @property string $xid
 * @property int $disbursement_id
 * @property string $disbursement_xid
 * @property \Carbon\Carbon $due_date
 * @property float $amount
 * @property string|\Sanf\Core\Modules\Installment\Enums\InstallmentStatusEnum $status
 * @property int|null $sequence_number
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|PaymentModel[] $payments
 */
class InstallmentModel extends AbstractModel
{
    protected $table = 'installment';

    protected $fillable = [
        'xid',
        'disbursement_id',
        'disbursement_xid',
        'due_date',
        'amount',
        'status',
        'sequence_number',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'amount' => 'float',
        'sequence_number' => 'integer',
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
