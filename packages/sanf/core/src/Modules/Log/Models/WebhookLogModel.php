<?php

namespace Sanf\Core\Modules\Log\Models;

use NbsPhp\Core\Models\AbstractModel;

/**
 * @property int $id
 * @property string $xid
 * @property string|\Sanf\Core\Modules\Log\Enums\WebhookLogKeyEnum $key
 * @property string $reference_id
 * @property array $payload
 * @property \Carbon\Carbon|null $received_at
 * @property \Carbon\Carbon|null $processed_at
 */
class WebhookLogModel extends AbstractModel
{
    protected $table = 'webhook_log';
    public $timestamps = false;

    protected $fillable = [
        'xid',
        'key',
        'reference_id',
        'payload',
        'received_at',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'received_at' => 'timestamp',
        'processed_at' => 'timestamp',
    ];
}
