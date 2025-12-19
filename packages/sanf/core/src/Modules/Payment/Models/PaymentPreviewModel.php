<?php

namespace Sanf\Core\Modules\Payment\Models;

use NbsPhp\Core\Models\AbstractModel;

/**
 * @property int $id
 * @property string $user_profile_xid
 * @property ?array $installments
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
}
