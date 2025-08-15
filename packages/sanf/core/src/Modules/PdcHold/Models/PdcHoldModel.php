<?php

namespace Sanf\Core\Modules\PdcHold\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use NbsPhp\Core\Models\AbstractModel;

/**
 * @property-read int $id
 * @property string $xid
 * @property int $user_id
 * @property string $customer_id
 * @property int $type @see \Sanf\Core\Modules\PdcHold\Enums\PdcHoldTypeEnum
 * @property Carbon|string $date_start
 * @property Carbon|string|null $date_end
 * @property int $reason_id
 * @property ?string $reason_value
 * @property int $status_id @see Sanf\Core\Modules\PdcHold\Enums\PdcHoldStatusEnum
 * @property ?string $status
 *
 * @property-read PdcHoldReasonModel $reason
 * @property-read Collection|PdcHoldGiroModel[] $giros
 *
 * @since CR2025
 *
 * @inheritDoc
 */
class PdcHoldModel extends AbstractModel
{
    protected $table = 'pdc_hold';

    protected $fillable = [
        'xid',
        'user_id',
        'customer_id',
        'type',
        'date_start',
        'date_end',
        'reason_id',
        'reason_value',
        'status_id',
        'status',
    ];

    protected $casts = [
        'date_start' => 'date',
        'date_end' => 'date',
    ];

    public function reason(): HasOne
    {
        return $this->hasOne(PdcHoldReasonModel::class, 'reason_id');
    }

    public function giros(): HasMany
    {
        return $this->hasMany(PdcHoldGiroModel::class, 'pdc_hold_id');
    }

    public function resume_giros(): HasMany
    {
        return $this->hasMany(PdcHoldGiroModel::class, 'pdc_resume_id');
    }
}
