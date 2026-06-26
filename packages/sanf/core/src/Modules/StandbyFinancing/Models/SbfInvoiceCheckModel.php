<?php

namespace Sanf\Core\Modules\StandbyFinancing\Models;

use Illuminate\Database\Eloquent\Model;

class SbfInvoiceCheckModel extends Model
{
    protected $table = 'sbf_invoice_checks';

    protected $fillable = [
        'cust_id',
        'no_plafond',
        'nomor_invoice',
        'total_invoice',
        'core_status',
        'core_message',
        'core_response',
    ];

    protected $casts = [
        'total_invoice' => 'integer',
        'core_response' => 'array',
    ];
}
