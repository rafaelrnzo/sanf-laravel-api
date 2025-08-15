<?php

namespace Sanf\Core\Modules\PdcHold\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use NbsPhp\Core\Models\AbstractModel;

/**
 * @property-read int $id
 * @property string $xid
 * @property int $pdc_hold_id
 * @property ?int $pdc_resume_id
 * @property string $customer_id
 * @property string $contract_no
 * @property string $pdc_no
 * @property float $amount
 * @property string $currency_type
 * @property Carbon|string $giro_date
 * @property ?string $pdc_type
 *
 * @property-read PdcHoldModel $pdc_hold
 * @property-read ?PdcHoldModel $pdc_resume
 *
 * @since CR2025
 *
 * @inheritDoc
 */
class PdcHoldGiroModel extends AbstractModel
{
    protected $table = 'pdc_hold_giros';

    protected $fillable = [
        'xid',
        'pdc_hold_id',
        'pdc_resume_id',
        'customer_id',
        'contract_no',
        'pdc_no',
        'amount',
        'currency_type',
        'giro_date',
        'pdc_type',
    ];

    protected $casts = [
        'giro_date' => 'date',
    ];

    public function pdc_hold(): BelongsTo
    {
        return $this->belongsTo(PdcHoldModel::class, 'pdc_hold_id');
    }

    public function pdc_resume(): BelongsTo
    {
        return $this->belongsTo(PdcHoldModel::class, 'pdc_resume_id');
    }
}
