<?php

namespace Sanf\Core\Modules\Payment\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use NbsPhp\Core\Models\AbstractModel;

/**
 * @property int $id
 * @property string $midtrans_order_id
 * @property string $midtrans_transaction_id
 * @property string $midtrans_snap_token
 * @property string $midtrans_snap_redirect_url
 * @property int $payment_id
 * @property string $payment_xid
 * @property float $gross_amount
 * @property string $payment_type
 * @property string|null $transaction_status
 * @property \Carbon\Carbon|null $transaction_time
 * @property string|null $fraud_status
 * @property array|null $raw_response
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read PaymentModel $payment
 */
class MidtransTransactionModel extends AbstractModel
{
    use SoftDeletes;

    protected $table = 'midtrans_transaction';

    protected $fillable = [
        'midtrans_order_id',
        'midtrans_transaction_id',
        'midtrans_snap_token',
        'midtrans_snap_redirect_url',
        'payment_id',
        'payment_xid',
        'gross_amount',
        'payment_type',
        'transaction_status',
        'transaction_time',
        'fraud_status',
        'raw_response',
        'version',
    ];

    protected $casts = [
        'gross_amount' => 'float',
        'admin_fee' => 'float',
        'transaction_time' => 'datetime',
        'raw_response' => 'array',
        'version' => 'int',
    ];

    public function payment()
    {
        return $this->belongsTo(PaymentModel::class, 'payment_id');
    }
}
