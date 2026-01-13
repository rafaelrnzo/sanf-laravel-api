<?php

namespace Sanf\Cron\Modules\Payment\Controllers;

use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\Payment\Jobs\PaymentExpireJob;
use Sanf\Core\Modules\Payment\UseCases\PaymentUseCase;

class CronPaymentController extends RestApiController
{
    public function paymentExpireCheck(
        PaymentUseCase $paymentUseCase
    )
    {
        $payments = $paymentUseCase->getPendingExpiredList();

        foreach ($payments as $payment) {
            dispatch(new PaymentExpireJob($payment));
        }

        return $this->responseOk();
    }
}
