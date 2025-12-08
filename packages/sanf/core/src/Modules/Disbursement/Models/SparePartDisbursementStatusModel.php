<?php

namespace Sanf\Core\Modules\Disbursement\Models;

use Illuminate\Database\Eloquent\Model;
use Sanf\Core\Constants\ConnectionDB;

/**
 * Spare Part Disbursement Status.
 *
 * @property int $id
 * @property string $name
 */
class SparePartDisbursementStatusModel extends Model
{
    protected $connection = ConnectionDB::PG_SQL_CMS;
    protected $table = 'spare_part_disbursement_status';

    protected $fillable = [
        'id',
        'name',
    ];

    public $incrementing = false;

    protected $casts = [
        'id' => 'integer',
    ];
}
