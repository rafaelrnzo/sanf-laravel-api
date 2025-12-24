<?php

namespace Sanf\Core\Modules\Payment\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Sanf\Core\Modules\Installment\Models\InstallmentModel;
use Sanf\Core\Modules\Payment\Entities\PaymentInstallmentSnapshotEntity;

/**
 * @property int $payment_id
 * @property int $installment_id
 * @property-read PaymentModel $payment
 * @property-read InstallmentModel $installment
 */
class PaymentInstallmentPivot extends Pivot
{
    protected $table = 'payment_installment';

    protected $fillable = [
        'installment_snapshot',
    ];

    protected $casts = [
        'installment_snapshot' => 'array',
    ];

    public function getInstallmentSnapshotAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        return new PaymentInstallmentSnapshotEntity($value);
    }
}
