<?php

namespace Sanf\Core\Modules\Payment\Models;

use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Modules\Payment\Entities\PaymentPreviewInstallmentEntity;

/**
 * @property int $id
 * @property string $user_profile_xid
 * @property PaymentPreviewInstallmentEntity[] $installments
 * @property string $platform
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class PaymentPreviewModel extends AbstractModel
{
    protected $table = 'payment_preview';

    protected $fillable = [
        'user_profile_xid',
        'installments',
        'platform',
    ];

    protected $casts = [
        'installments' => 'array',
    ];

    public function getInstallmentsAttribute($value)
    {
        if (empty($value)) {
            return [];
        }

        if (is_string($value)) {
            $value = json_decode($value, true) ?: [];
        }

        return array_map(
            fn ($installment) => new PaymentPreviewInstallmentEntity([
                'contract_no' => $installment['contract_no'],
                'due_date' => $installment['due_date'],
            ]),
            $value
        );
    }
}
