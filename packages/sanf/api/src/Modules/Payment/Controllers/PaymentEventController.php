<?php

namespace Sanf\Api\Modules\Payment\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Exceptions\ResourceNotFoundException;
use Sanf\Api\Modules\Payment\Support\MidtransHelper;
use Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum;
use Sanf\Core\Modules\Payment\Events\PaymentCompletedEvent;
use Sanf\Core\Modules\Payment\Exceptions\PaymentSettledException;
use Sanf\Core\Modules\Payment\Jobs\PaymentExpireJob;
use Sanf\Core\Modules\Payment\Models\PaymentModel;
use Sanf\Core\Modules\Payment\Payloads\MidtransWebhookPayload;
use Sanf\Core\Modules\Payment\UseCases\CheckPaymentByMidtransTransactionUseCase;
use Sanf\Core\Modules\Payment\UseCases\HandleMidtransCallbackUseCase;
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
        PaymentUseCase $paymentUseCase,
        HandleMidtransCallbackUseCase $midtransCallbackUseCase
    ) {
        $formData = $this->validate($request, [
            'payment_xid' => ['required', 'string'],
            'midtrans_transaction.order_id' => ['required', 'string'],
            'midtrans_transaction.status_code' => ['required', 'string'],
            'midtrans_transaction.transaction_id' => ['nullable', 'string'],
            'midtrans_transaction.transaction_status' => ['nullable', 'string'],
            'midtrans_transaction.transaction_time' => ['nullable', 'string'],
            'midtrans_transaction.fraud_status' => ['nullable', 'string'],
            'midtrans_transaction.gross_amount' => ['required', 'string'],
            'midtrans_transaction.signature_key' => ['required', 'string'],
            'midtrans_transaction.payment_type' => ['nullable', 'string'],
            'midtrans_transaction.settlement_time' => ['nullable', 'string'],
        ]);

        $payload = new MidtransWebhookPayload(array_merge(
            $formData['midtrans_transaction'],
            [
                'raw_request' => $request->input('midtrans_transaction'),
            ]
        ));

        MidtransHelper::validateSignature(
            $payload->signature_key,
            $payload->order_id,
            $payload->status_code,
            $payload->gross_amount
        );

        $paymentXid = $formData['payment_xid'];
        $midtransOrderId = $payload->order_id;

        $payment = $paymentUseCase->findByXidAndMidtransOrder($paymentXid, $midtransOrderId);

        if ($payment === null) {
            throw new ResourceNotFoundException('Payment not found');
        }

        try {
            /**
             * @var PaymentModel
             */
            $latestPayment = DB::transaction(function () use ($midtransCallbackUseCase, $payload) {
                return $midtransCallbackUseCase->execute($payload);
            });

            if ($payment->status === PaymentStatusEnum::PENDING && $latestPayment->status === PaymentStatusEnum::SUCCESS) {
                event(new PaymentCompletedEvent($latestPayment));
            }
        } catch (PaymentSettledException $e) {
            // nothing todo
        }

        return $this->responseOk();
    }
}
