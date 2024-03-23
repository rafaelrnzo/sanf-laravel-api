<?php

namespace Sanf\Core\Modules\Prepayment\Models;

use NbsPhp\Core\Models\AbstractModel;

class PrepaymentSubmissionHistoryModel extends AbstractModel
{
    public const UPDATED_AT = null;

    protected $table = 'prepayment_submission_history';

    protected $casts = [
        'created_by' => 'object',
    ];
}
