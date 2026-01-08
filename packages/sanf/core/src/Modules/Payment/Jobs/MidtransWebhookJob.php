<?php

namespace Sanf\Core\Modules\Payment\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Sanf\Core\Modules\Log\Enums\WebhookLogKeyEnum;
use Sanf\Core\Modules\Log\Payloads\CreateWebhookLogPayload;
use Sanf\Core\Modules\Log\UseCases\WebhookLogUseCase;
use Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum;
use Sanf\Core\Modules\Payment\Events\PaymentCompletedEvent;
use Sanf\Core\Modules\Payment\Exceptions\MidtransInvalidSignatureException;
use Sanf\Core\Modules\Payment\Payloads\MidtransWebhookPayload;
use Sanf\Core\Modules\Payment\UseCases\CheckPaymentStatusUseCase;
use Sanf\Core\Modules\Payment\UseCases\FindPaymentByMidtransOrderUseCase;

class MidtransWebhookJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    protected $payload;

    public function __construct(MidtransWebhookPayload $payload)
    {
        $this->payload = $payload;
    }

    public function handle(
        FindPaymentByMidtransOrderUseCase $findPaymentUseCase,
        CheckPaymentStatusUseCase $checkPaymentStatusUseCase,
        WebhookLogUseCase $webhookLogUseCase
    )
    {
        $this->validateSignature();

        $payment = $this->payload->order_id ? $findPaymentUseCase->execute($this->payload->order_id) : null;

        if ($payment === null) {
            return false;
        }

        // TODO: fill midtrans transaction id if empty, to handle Danamon VA & BSI VA can only use transaction id for status check

        DB::transaction(function () use ($checkPaymentStatusUseCase, $payment) {
            $newStatus = $checkPaymentStatusUseCase->execute(
                $payment->xid,
                $payment->user_auth_id,
                $payment->user_profile_xid,
            );

            if ($payment->status === PaymentStatusEnum::PENDING && $newStatus === PaymentStatusEnum::SUCCESS) {
                event(new PaymentCompletedEvent($payment));
            }
        });

        $webhookLogUseCase->create(new CreateWebhookLogPayload([
            'xid' => nano_id(),
            'key' => WebhookLogKeyEnum::MIDTRANS_STATUS,
            'reference_id' => $this->payload->order_id,
            'payload' => $this->payload->raw_request,
            'received_at' => $this->payload->received_at,
            'processed_at' => (string) Carbon::now(),
        ]));
    }

    private function validateSignature(): void
    {
        $payload = $this->payload;

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
