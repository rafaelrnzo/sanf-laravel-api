<?php

namespace Sanf\Core\Modules\Plafond\Models;

use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Constants\ConnectionDB;

class PlafondDisbursementDocumentModel extends AbstractModel
{
    protected $connection = ConnectionDB::PG_SQL;

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $table = 'plafond_disbursement_document';

    protected $fillable = [
        'xid',
        'plafond_disbursement_id',
        'submission_id',
        'origin_name',
        'file_name',
        'path',
        'metadata',
        'order_no',
        'version',
        'created_at',
        'updated_at',
    ];
}
