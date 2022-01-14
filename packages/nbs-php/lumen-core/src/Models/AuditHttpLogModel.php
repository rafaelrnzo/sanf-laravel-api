<?php

namespace NbsPhp\Core\Models;

class AuditHttpLogModel extends AbstractModel
{
    protected $table = 'audit_http_log';

    protected $fillable = [
        'id',
        'user_id',
        'request_id',
        'method',
        'name',
        'path',
        'header',
        'query',
        'body',
        'response',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'header' => 'object',
        'query' => 'object',
        'body' => 'object',
        'response' => 'object'
    ];

    const UPDATED_AT = null;
}
