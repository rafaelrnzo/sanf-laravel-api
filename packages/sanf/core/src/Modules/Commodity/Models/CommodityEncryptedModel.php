<?php

namespace Sanf\Core\Modules\Commodity\Models;

use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Modules\User\AuthEncryptedModel;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class CommodityEncryptedModel extends CommodityModel
{
    use SodiumEncryptionTrait;

    protected $connection = ConnectionDB::PG_SODIUM;

    protected $table = 'commodity_encrypted';

    protected $casts = [
        'image_file' => 'object',
    ];

    protected $hidden = [
        'nonce',
    ];

    public function user()
    {
        return $this->belongsTo(AuthEncryptedModel::class, 'user_id');
    }

    public function getLocationMetadataAttribute()
    {
        return json_decode($this->decryptor()->decrypt($this->attributes['location_metadata']));
    }

    public function getPhoneNumberAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['phone_number']);
    }

    public function getWhatsappNumberAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['whatsapp_number']);
    }

    public function getBusinessEmailAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['business_email']);
    }

    public function getModifiedByAttribute()
    {
        return json_decode($this->decryptor()->decrypt($this->attributes['modified_by']));
    }

    public function getCityNameAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['city_name']);
    }

    public function getProvinceNameAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['province_name']);
    }
}
