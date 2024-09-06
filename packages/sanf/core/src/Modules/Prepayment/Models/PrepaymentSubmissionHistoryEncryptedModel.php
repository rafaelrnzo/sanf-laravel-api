<?php

namespace Sanf\Core\Modules\Prepayment\Models;

use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class PrepaymentSubmissionHistoryEncryptedModel extends AbstractModel
{
    use SodiumEncryptionTrait;

    public const UPDATED_AT = null;

    protected $table = 'prepayment_submission_history';

    protected $hidden = [
        'nonce',
    ];

    public function getCreatedByAttribute()
    {
        return json_decode($this->decryptor()->decrypt($this->attributes['created_by']));
    }
}
