<?php

namespace Sanf\Core\Modules\Survey\Models;

use Sanf\Core\Traits\SodiumEncryptionTrait;

class SurveyEncryptedModel extends SurveyModel
{
    use SodiumEncryptionTrait;

    protected $hidden = [
        'nonce',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->fillable[] = 'nonce';
    }

    public function getCustomerNameAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['customer_name']);
    }

    public function getPicNameAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['pic_name']);
    }
}
