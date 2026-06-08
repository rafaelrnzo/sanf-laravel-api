<?php

namespace Sanf\Core\Modules\StandbyFinancing\Models;

use Illuminate\Database\Eloquent\Model;

class StandbyFinancingApplicationModel extends Model
{
    protected $table = 'standby_financing_applications';

    protected $fillable = [
        'recap_id_b2b',
        'cust_id',
        'no_plafond',
        'p_code',
        'supplier_id',
        'supplier_name',
        'total_invoice',
        'total_amount',
        'currency',
        'period_start',
        'period_end',
        'tenor',
        'tenor_type',
        'payment_method',
        'state',
        'state_code',
        'source_channel',
        'agreement_checkbox',
        'request_id',
        'created_by_user_id',
        'submitted_at',
    ];

    protected $casts = [
        'agreement_checkbox' => 'boolean',
        'period_start' => 'date:Y-m-d',
        'period_end' => 'date:Y-m-d',
        'submitted_at' => 'datetime',
        'total_amount' => 'float',
    ];

    public function invoices()
    {
        return $this->hasMany(StandbyFinancingInvoiceModel::class, 'application_id');
    }

    public function bankAccount()
    {
        return $this->hasOne(StandbyFinancingBankAccountModel::class, 'application_id');
    }

    public function documents()
    {
        return $this->hasMany(StandbyFinancingDocumentModel::class, 'application_id');
    }

    public function plafondLock()
    {
        return $this->hasOne(StandbyFinancingPlafondLockModel::class, 'application_id');
    }
}
