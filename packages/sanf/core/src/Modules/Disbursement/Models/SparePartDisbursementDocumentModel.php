<?php

namespace Sanf\Core\Modules\Disbursement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sanf\Core\Constants\ConnectionDB;

/**
 * Spare Part Disbursement Document.
 *
 * @property int $id
 * @property string $xid
 * @property int $disbursement_batch_id
 * @property string $disbursement_batch_xid
 * @property ?string $doc_id
 * @property ?string $doc_desc
 * @property bool $doc_required
 * @property ?array $doc_file
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $version
 * @property-read SparePartDisbursementBatchModel $batch
 */
class SparePartDisbursementDocumentModel extends Model
{
    use SoftDeletes;

    protected $connection = ConnectionDB::PG_SQL_CMS;
    protected $table = 'spare_part_disbursement_document';

    protected $fillable = [
        'xid',
        'disbursement_batch_id',
        'disbursement_batch_xid',
        'doc_id',
        'doc_desc',
        'doc_required',
        'doc_file',
        'version',
    ];

    protected $casts = [
        'doc_required' => 'boolean',
        'doc_file' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(SparePartDisbursementBatchModel::class, 'disbursement_batch_id');
    }
}
