<?php

namespace Sanf\Core\Modules\StandbyFinancing\Models;

use Illuminate\Database\Eloquent\Model;

class StandbyFinancingInvoiceModel extends Model
{
    protected $table = 'standby_financing_invoices';

    protected $fillable = [
        'application_id',
        'recap_id',
        'invoice_number',
        'invoice_date',
        'currency',
        'amount',
        'document_id',
        'invoice_status',
        'order_no',
    ];

    protected $casts = [
        'invoice_date' => 'date:Y-m-d',
        'amount' => 'float',
    ];
}
