<?php

namespace Sanf\Core\Modules\Plafond\Models;

use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class PaymentAccelarationDocumentEncryptedModel extends AbstractModel
{
    use SodiumEncryptionTrait;

    protected $connection = ConnectionDB::PG_SODIUM;

    protected $table = 'payment_acc_document_encrypted';

    protected $fillable = [
        'xid',
        'client_id',
        'plafond_id',
        'company',
        'bowheer',
        'bowheer_email',
        'document_no',
        'document_date',
        'first_signer_company',
        'first_signer_name',
        'first_signer_position',
        'second_signer_company',
        'second_signer_name',
        'second_signer_position',
        'origin',
        'filename',
        'path',
        'metadata',
        'invoices',
        'bowheer_address',
        'nonce',
    ];

    protected $hidden = [
        'nonce',
    ];

    public function getCompanyAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['company']);
    }

    public function getBowheerAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['bowheer']);
    }

    public function getBowheerEmailAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['bowheer_email']);
    }

    public function getFirstSignerCompanyAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['first_signer_company']);
    }

    public function getFirstSignerNameAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['first_signer_name']);
    }

    public function getFirstSignerPositionAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['first_signer_position']);
    }

    public function getSecondSignerCompanyAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['second_signer_company']);
    }

    public function getSecondSignerNameAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['second_signer_name']);
    }

    public function getSecondSignerPositionAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['second_signer_position']);
    }

    public function getBowheerAddressAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['bowheer_address']);
    }
}
