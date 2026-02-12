<?php

namespace Sanf\Dashboard\Modules\User\Models;

use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;

/**
 * @property string $BowheerId
 * @property string $BowheerEmail
 * @property string $BowheerName
 * @property string $BowheerCode
 * @property int $userAuthId
 * @property ?array $partnerProfile
 */
class CustomerBindingEncryptedModel extends AbstractModel
{
    use SodiumEncryptionTrait;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $connection = ConnectionDB::PG_SODIUM_CMS;

    protected $table = 'CustomerBindingEncrypted';

    public static $snakeAttributes = false;

    protected $fillable = [
        'BowheerId',
        'BowheerEmail',
        'BowheerName',
        'BowheerCode',
        'userAuthId',
        'createdAt',
        'updatedAt',
        'nonce',
        'partnerProfile',
        'CustomerId',
        'createdById',
        'createdBy',
        'modifiedBy',
        'version',
        'metadata',
    ];

    protected $hidden = [
        'nonce',
    ];

    public $incrementing = false;

    public function getBowheerEmailAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['BowheerEmail']);
    }

    public function getBowheerNameAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['BowheerName']);
    }

    public function getCreatedByAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['createdBy']);
    }

    public function getModifiedByAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['modifiedBy']);
    }

    public function getPartnerProfileAttribute()
    {
        return json_decode($this->decryptor()->decrypt($this->attributes['partnerProfile']));
    }

    public function toArray()
    {
        $data = parent::toArray();

        if(array_key_exists('BowheerName', $data)) {
            $data['BowheerName'] = $this->BowheerName;
        }

        if(array_key_exists('BowheerEmail', $data)) {
            $data['BowheerEmail'] = $this->BowheerEmail;
        }

        return $data;
    }
}
