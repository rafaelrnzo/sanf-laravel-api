<?php

namespace Sanf\Core\Modules\Payment\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Sanf\Core\Modules\Installment\Models\InstallmentModel;

/**
 * @property int $payment_id
 * @property int $installment_id
 * @property-read PaymentModel $payment
 * @property-read InstallmentModel $installment
 */
class PaymentInstallmentPivot extends Pivot
{
    protected $table = 'payment_installment';
}
