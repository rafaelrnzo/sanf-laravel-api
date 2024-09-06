<?php

namespace Sanf\Core\Modules\Contract\Models;

use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class UserTekenAjaEncryptedModel extends AbstractModel
{
    use SodiumEncryptionTrait;

    protected $connection = ConnectionDB::PG_SODIUM;

    protected $table = 'user_tekenaja_encrypted';

    protected $fillable = [
        'email',
        'msisdn',
        'nik',
        'full_name',
        'dob',
        'pob',
        'gender',
        'address',
        'postal_code',
        'province_id',
        'district_id',
        'sub_district_id',
        'selfie_file',
        'identity_file',
        'total_submit_registration',
        'status_id',
        'updated_at',
        'nonce',
    ];

    protected $hidden = [
        'nonce',
    ];

    public function getEmailAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['email']);
    }

    public function getMsisdnAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['msisdn']);
    }

    public function getNikAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['nik']);
    }

    public function getFullNameAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['full_name']);
    }

    public function getDobAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['dob']);
    }

    public function getPobAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['pob']);
    }

    public function getGenderAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['gender']);
    }

    public function getAddressAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['address']);
    }

    public function getPostalCodeAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['postal_code']);
    }

    public function getSelfieFileAttribute()
    {
        return json_decode($this->decryptor()->decrypt($this->attributes['selfie_file']));
    }

    public function getIdentityFileAttribute()
    {
        return json_decode($this->decryptor()->decrypt($this->attributes['identity_file']));
    }
}
