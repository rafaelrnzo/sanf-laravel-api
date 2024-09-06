<?php

namespace Sanf\Core\Modules\Scanina\Models;

use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class ScaninaProductCartEncryptedModel extends ScaninaProductCartModel
{
    use SodiumEncryptionTrait;

    protected $connection = ConnectionDB::PG_SQL;

    protected $casts = [];

    protected $hidden = [
        'nonce',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->fillable[] = 'nonce';
    }

    public function getSnapshotRequestBodyAttribute()
    {
        return json_decode($this->decryptor()->decrypt($this->attributes['snapshot_request_body']));
    }

    public function getSnapshotResponseBodyAttribute()
    {
        return json_decode($this->decryptor()->decrypt($this->attributes['snapshot_response_body']));
    }
}
