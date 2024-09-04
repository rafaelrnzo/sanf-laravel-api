<?php

namespace Sanf\Core\Modules\Location;

use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class LocationEncryptedModel extends LocationModel
{
    use SodiumEncryptionTrait;

    protected $connection = ConnectionDB::PG_SODIUM;

    protected $table = 'm_location_encrypted';

    protected $hidden = [
        'nonce',
    ];

    public function getNameAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['name']);
    }

    public function getMetadataAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['metadata']);
    }

    public function getModifiedByAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['modified_by']);
    }
}
