<?php

namespace Sanf\Api\Modules\Payment\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Api\Modules\Payment\Support\MidtransPaymentMethodResolver;
use Sanf\Core\Modules\Payment\Models\PaymentModel;

final class PaymentMethodTransformer extends TransformerAbstract
{
    public function transform(PaymentModel $model)
    {
        $midtransTransaction = $model->midtransTransaction;

        if (!$midtransTransaction) {
            return MidtransPaymentMethodResolver::emptyResult()->toArray();
        }

        return MidtransPaymentMethodResolver::resolve($midtransTransaction->raw_response)->toArray();
    }
}
