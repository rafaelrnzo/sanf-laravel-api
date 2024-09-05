<?php

namespace Sanf\Core\Modules\HttpLog\Models;

use NbsPhp\Core\Models\ApiRequestLogModel;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class ApiRequestLogEncryptedModel extends ApiRequestLogModel
{
    use SodiumEncryptionTrait;

    protected $casts = [
        'header' => 'object',
    ];

    protected $hidden = [
        'nonce',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->fillable[] = 'nonce';
    }

    public function getPathAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['path']);
    }

    public function getQueryAttribute()
    {
        return json_decode($this->decryptor()->decrypt($this->attributes['query']));
    }

    public function getBodyAttribute()
    {
        return json_decode($this->decryptor()->decrypt($this->attributes['body']));
    }

    public function getResponseAttribute()
    {
        return json_decode($this->decryptor()->decrypt($this->attributes['response']));
    }
}
