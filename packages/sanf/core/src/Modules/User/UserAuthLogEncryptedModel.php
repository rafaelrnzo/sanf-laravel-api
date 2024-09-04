<?php

namespace Sanf\Core\Modules\User;

use Sanf\Core\Traits\SodiumEncryptionTrait;

class UserAuthLogEncryptedModel extends UserAuthLogModel
{
    use SodiumEncryptionTrait;

    protected $hidden = [
        'nonce',
    ];

    public function getEmailAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['email']);
    }

    public function getCreatedByAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['created_by']);
    }

    public function user()
    {
        return $this->belongsTo(AuthEncryptedModel::class, 'user_id');
    }
}
