<?php

namespace Sanf\Core\Modules\PdcHold\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use NbsPhp\Core\Models\AbstractModel;

/**
 * @property-read int $id
 * @property string $xid
 * @property string $name
 * @property bool $has_free_text
 * @property int $order
 *
 * @since CR2025
 *
 * @inheritDoc
 */
class PdcHoldReasonModel extends AbstractModel
{
    use SoftDeletes;

    protected $table = 'pdc_hold_reasons';

    protected $fillable = [
        'xid',
        'name',
        'has_free_text',
        'order',
    ];
}
