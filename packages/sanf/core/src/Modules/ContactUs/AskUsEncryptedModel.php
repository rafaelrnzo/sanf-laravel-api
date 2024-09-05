<?php

namespace Sanf\Core\Modules\ContactUs;

use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class AskUsEncryptedModel extends AskUsModel
{
    use SodiumEncryptionTrait;

    protected $connection = ConnectionDB::PG_SQL;

    protected $hidden = [
        'nonce',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->fillable[] = 'nonce';
    }

    public function getNameAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['name']);
    }

    public function getPhoneNumberAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['phone_number']);
    }

    public function getEmailAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['email']);
    }

    public function getModifiedByAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['modified_by']);
    }
}
