<?php

namespace Sanf\Core\Modules\ContactUs;

use NbsPhp\Core\Models\AbstractModel;

class AskUsModel extends AbstractModel
{
    protected $table = 'ask_us_questioner';

    protected $fillable = [
        'topic_id',
        'title',
        'message',
        'name',
        'email',
        'phone_number',
        'contract_no',
        'contact_media',
        'contact_time',
        'images',
        'created_at',
        'updated_at',
        'modified_by',
    ];
}
