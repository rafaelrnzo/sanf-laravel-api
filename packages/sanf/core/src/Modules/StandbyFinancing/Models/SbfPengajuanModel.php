<?php

namespace Sanf\Core\Modules\StandbyFinancing\Models;

use Illuminate\Database\Eloquent\Model;

class SbfPengajuanModel extends Model
{
    protected $table = 'sbf_pengajuan';

    protected $fillable = [
        'cust_id',
        'no_plafond',
        'period_start',
        'period_end',
        'tenor',
        'total_invoice_count',
        'total_amount',
        'bank_id',
        'bank_owner',
        'bank_provider',
        'bank_account_number',
        'supplier_payload',
        'invoice_document',
        'spt_dokument',
        'supporting_dokuments',
        'local_status',
        'core_recap_id',
        'core_status',
        'core_message',
        'core_response',
        'submitted_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'tenor' => 'integer',
        'total_invoice_count' => 'integer',
        'total_amount' => 'integer',
        'supplier_payload' => 'array',
        'invoice_document' => 'array',
        'spt_dokument' => 'array',
        'supporting_dokuments' => 'array',
        'core_response' => 'array',
        'submitted_at' => 'datetime',
    ];

    public function bankAccounts()
    {
        return $this->hasMany(SbfPengajuanBankAccountModel::class, 'pengajuan_id');
    }
}
