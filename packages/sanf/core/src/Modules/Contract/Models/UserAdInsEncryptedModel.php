<?php

namespace Sanf\Core\Modules\Contract\Models;

use Illuminate\Support\Facades\Crypt;
use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class UserAdInsEncryptedModel extends AbstractModel
{
    use SodiumEncryptionTrait;

    protected $connection = ConnectionDB::PG_SODIUM;

    protected $table = 'user_adins_encrypted';

    protected $hidden = [
        'password',
        'nonce',
    ];

    protected $fillable = [
        'xid',
        'user_id',
        'sanf_id',
        'email',
        'msisdn',
        'identity_no',
        'full_name',
        'date_of_birth',
        'place_of_birth',
        'gender',
        'address',
        'postal_code',
        'province',
        'city',
        'district',
        'sub_district',
        'selfie_file',
        'identity_file',
        'status_id',
        'password',
        'created_at',
        'updated_at',
        'nonce',
    ];

    public function getPasswordDecryptAttribute()
    {
        return Crypt::decryptString($this->password);
    }

    public function getEmailAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['email']);
    }

    public function getMsisdnAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['msisdn']);
    }

    public function getIdentityNoAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['identity_no']);
    }

    public function getFullNameAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['full_name']);
    }

    public function getDateOfBirthAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['date_of_birth']);
    }

    public function getPlaceOfBirthAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['place_of_birth']);
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

    public function getProvinceAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['province']);
    }

    public function getCityAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['city']);
    }

    public function getDistrictAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['district']);
    }

    public function getSubDistrictAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['sub_district']);
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
