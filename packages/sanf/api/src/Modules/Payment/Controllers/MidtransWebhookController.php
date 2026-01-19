<?php

namespace Sanf\Api\Modules\Payment\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Exceptions\ResourceNotFoundException;
use Sanf\Core\Modules\Log\Enums\WebhookLogKeyEnum;
use Sanf\Core\Modules\Log\Payloads\CreateWebhookLogPayload;
use Sanf\Core\Modules\Log\UseCases\WebhookLogUseCase;
use Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum;
use Sanf\Core\Modules\Payment\Events\PaymentCompletedEvent;
use Sanf\Core\Modules\Payment\Exceptions\MidtransInvalidSignatureException;
use Sanf\Core\Modules\Payment\Models\PaymentModel;
use Sanf\Core\Modules\Payment\Payloads\MidtransWebhookPayload;
use Sanf\Core\Modules\Payment\UseCases\CheckPaymentByMidtransTransactionUseCase;
use Sanf\Core\Modules\Payment\UseCases\FindPaymentByMidtransOrderUseCase;
use Sanf\Core\Modules\Payment\UseCases\HandleMidtransCallbackUseCase;

final class MidtransWebhookController extends RestApiController
{
    public function postHandle(
        Request $request,
        FindPaymentByMidtransOrderUseCase $findPaymentUseCase,
        CheckPaymentByMidtransTransactionUseCase $checkPaymentUseCase,
        WebhookLogUseCase $webhookLogUseCase,
        HandleMidtransCallbackUseCase $midtransCallbackUseCase
    )
    {
        $form = $this->validate($request, [
            'order_id' => ['nullable', 'string'],
            'status_code' => ['nullable', 'string'],
            'transaction_id' => ['nullable', 'string'],
            'transaction_status' => ['nullable', 'string'],
            'transaction_time' => ['nullable', 'string'],
            'fraud_status' => ['nullable', 'string'],
            'gross_amount' => ['nullable'],
            'signature_key' => ['nullable', 'string'],
            'payment_type' => ['nullable', 'string'],
            'settlement_time' => ['nullable', 'string'],
        ]);

        $payload = new MidtransWebhookPayload(array_merge(
            $form,
            [
                'received_at' => (string) Carbon::now(),
                'raw_request' => $request->all(),
            ]
        ));

        $this->validateSignature($payload);

        $payment = $payload->order_id ? $findPaymentUseCase->execute($payload->order_id) : null;

        if ($payment === null) {
            throw new ResourceNotFoundException('Payment not found');
        }

        /**
         * @var PaymentModel
         */
        $latestPayment = DB::transaction(function () use ($midtransCallbackUseCase, $payload) {
            return $midtransCallbackUseCase->execute($payload);
        });

        if ($payment->status === PaymentStatusEnum::PENDING && $latestPayment->status === PaymentStatusEnum::SUCCESS) {
            event(new PaymentCompletedEvent($latestPayment));
        }

        $webhookLogUseCase->create(new CreateWebhookLogPayload([
            'xid' => nano_id_alphanumeric(),
            'key' => WebhookLogKeyEnum::MIDTRANS_STATUS,
            'reference_id' => $payload->order_id,
            'payload' => $payload->raw_request,
            'received_at' => $payload->received_at,
            'processed_at' => (string) Carbon::now(),
        ]));

        return $this->responseOk('OK', [
            'processed' => true,
        ]);
    }

    private function validateSignature(MidtransWebhookPayload $payload): void
    {
        $signature = $payload->signature_key;

        $serverKey = config('midtrans.server_key');

        if (empty($signature)) {
            return;
        }

        $orderId = $payload->order_id;
        $statusCode = $payload->status_code;
        $grossAmount = $payload->gross_amount;

        if (!$orderId || !$statusCode || !$grossAmount) {
            throw new MidtransInvalidSignatureException();
        }

        $hashed = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if (!hash_equals($hashed, $signature)) {
            throw new MidtransInvalidSignatureException();
        }
    }
}
