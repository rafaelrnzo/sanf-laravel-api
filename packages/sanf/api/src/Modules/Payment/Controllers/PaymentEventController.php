<?php

namespace Sanf\Api\Modules\Payment\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Exceptions\ResourceNotFoundException;
use Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum;
use Sanf\Core\Modules\Payment\Events\PaymentCompletedEvent;
use Sanf\Core\Modules\Payment\Exceptions\PaymentSettledException;
use Sanf\Core\Modules\Payment\Jobs\PaymentExpireJob;
use Sanf\Core\Modules\Payment\Models\PaymentModel;
use Sanf\Core\Modules\Payment\UseCases\CheckPaymentByMidtransTransactionUseCase;
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

    public function completed(
        Request $request,
        CheckPaymentByMidtransTransactionUseCase $checkPaymentUseCase,
        PaymentUseCase $paymentUseCase
    ) {
        $formData = $this->validate($request, [
            'payment_xid' => 'required',
            'midtrans_order_id' => 'required',
            'midtrans_transaction_id' => 'required',
        ]);

        $paymentXid = $formData['payment_xid'];
        $midtransOrderId = $formData['midtrans_order_id'];
        $midtransTransactionId = $formData['midtrans_transaction_id'];

        $payment = $paymentUseCase->findByXidAndMidtransOrder($paymentXid, $midtransOrderId);

        if ($payment === null) {
            throw new ResourceNotFoundException('Payment not found');
        }

        /**
         * @var PaymentModel
         */
        $latestPayment = DB::transaction(function () use ($checkPaymentUseCase, $midtransTransactionId) {
            return $checkPaymentUseCase->execute($midtransTransactionId);
        });

        if ($payment->status === PaymentStatusEnum::PENDING && $latestPayment->status === PaymentStatusEnum::SUCCESS) {
            event(new PaymentCompletedEvent($latestPayment));
        }

        return $this->responseOk();
    }
}
