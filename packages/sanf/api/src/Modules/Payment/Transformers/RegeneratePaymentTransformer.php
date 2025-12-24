<?php

namespace Sanf\Api\Modules\Payment\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\Payment\Models\PaymentModel;

final class RegeneratePaymentTransformer extends TransformerAbstract
{
    public function transform(PaymentModel $model)
    {
        $midtransTransaction = $model->activeMidtransTransaction;

        return [
            'snap_midtrans' => [
                'token' => $midtransTransaction->midtrans_snap_token,
                'redirect_url' => $midtransTransaction->midtrans_snap_redirect_url,
            ],
        ];
    }
}
