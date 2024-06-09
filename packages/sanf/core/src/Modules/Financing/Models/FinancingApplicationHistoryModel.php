<?php

namespace Sanf\Core\Modules\Financing\Models;

use NbsPhp\Core\Models\AbstractModel;

class FinancingApplicationHistoryModel extends AbstractModel
{
    const UPDATED_AT = null;

    protected $table = 'financing_application_history';

    public $fillable = [
        'application_id',
        'status_id',
        'created_by',
        'method_id',
        'facility_id',
        'amount',
        'down_payment_amount',
        'down_payment_percentage',
        'tax_amount',
        'vat_amount',
        'backharge_amount',
        'other_amount',
        'total_amount',
        'tenor',
        'request_snapshot',
        'client',
    ];
}
