<?php

namespace NbsPhp\Core\Models;

class ApiRequestLogModel extends AbstractModel
{
    protected $table = 'api_request_log';

    protected $fillable = [
        'id',
        'user_id',
        'request_id',
        'status_code',
        'host',
        'method',
        'path',
        'header',
        'query',
        'body',
        'response',
        'created_at',
    ];

    protected $casts = [
        'header' => 'object',
        'query' => 'object',
        'body' => 'object',
        'response' => 'object',
    ];

    const UPDATED_AT = null;
}
