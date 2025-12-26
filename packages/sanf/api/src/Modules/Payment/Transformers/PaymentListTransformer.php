<?php

namespace Sanf\Api\Modules\Payment\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\Payment\Models\PaymentModel;

final class PaymentListTransformer extends TransformerAbstract
{
    public function transform(PaymentModel $model)
    {
        $midtransTransaction = $model->midtransTransaction;

        return [
            'xid' => $model->xid,
            'total_amount' => (float) $model->amount,
            'category' => $model->category,
            'status' => $model->status,
            'payment_method' => fractal($model, PaymentMethodTransformer::class),
            'snap_midtrans' => [
                'token' => optional($midtransTransaction)->midtrans_snap_token,
                'redirect_url' => optional($midtransTransaction)->midtrans_snap_redirect_url,
            ],
            'currency' => $model->currency,
            'contract_nums' => $model->installments->pluck('contract_no')->toArray(),
            'due_date' => nullable_unix_timestamp($model->expired_at),
            'created_at' => nullable_unix_timestamp($model->created_at),
            'updated_at' => nullable_unix_timestamp($model->updated_at),
        ];
    }
}
