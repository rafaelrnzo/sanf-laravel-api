<?php

namespace Sanf\Core\Modules\StandbyFinancing\Models;

use Illuminate\Database\Eloquent\Model;

class SbfPengajuanBankAccountModel extends Model
{
    protected $table = 'sbf_pengajuan_bank_accounts';

    protected $fillable = [
        'pengajuan_id',
        'bank_id',
        'bank_owner',
        'bank_provider',
        'bank_account_number',
        'source',
    ];

    protected $casts = [
        'pengajuan_id' => 'integer',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(SbfPengajuanModel::class, 'pengajuan_id');
    }
}
