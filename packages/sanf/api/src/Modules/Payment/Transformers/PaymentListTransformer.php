<?php

namespace Sanf\Api\Modules\Payment\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\Payment\Models\PaymentModel;

final class PaymentListTransformer extends TransformerAbstract
{
    public function transform(PaymentModel $model)
    {
        // TODO: adjust payment method
        return [
            'xid' => $model->xid,
            'total_amount' => (float) $model->amount,
            'category' => $model->category,
            'status' => $model->status,
            'payment_method' => [
                'type' => 'virtual_account',
                'provider' => 'BCA',
                'virtual_account_number' => '1234567890123456',
                'provider_logo_url' => 'https://localhost/midtrans-logo/bca.jpg',
            ],
            'currency' => $model->currency,
            'contract_nums' => $model->installments->pluck('contract_no')->toArray(),
            'due_date' => nullable_unix_timestamp($model->expired_at),
            'created_at' => nullable_unix_timestamp($model->created_at),
            'updated_at' => nullable_unix_timestamp($model->updated_at),
        ];
    }
}
