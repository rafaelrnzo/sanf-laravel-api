<?php

namespace Sanf\Api\Modules\Payment\Controllers;

use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Exceptions\ResourceNotFoundException;
use Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum;
use Sanf\Core\Modules\Payment\Exceptions\PaymentSettledException;
use Sanf\Core\Modules\Payment\Jobs\PaymentExpireJob;
use Sanf\Core\Modules\Payment\UseCases\PaymentUseCase;

class PaymentEventController extends RestApiController
{
    public function created(Request $request, PaymentUseCase $paymentUseCase)
    {
        $formData = $this->validate($request, [
            'paymentXid' => 'required',
        ]);

        $paymentXid = $formData['paymentXid'];
        $payment = $paymentUseCase->findByXid($paymentXid);

        if ($payment === null) {
            throw new ResourceNotFoundException('Payment not found');
        }

        if ($payment->status !== PaymentStatusEnum::PENDING) {
            throw new PaymentSettledException();
        }

        // TODO: handle can only dispatch job once
        dispatch((new PaymentExpireJob($payment))->delay($payment->expired_at));

        return $this->responseOk();
    }
}
