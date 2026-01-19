<?php

namespace Sanf\Core\Modules\Payment\Payloads;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class MidtransWebhookPayload extends FlexibleDataTransferObject
{
    public ?string $received_at;
    public ?string $transaction_status;
    public ?string $transaction_id;
    public ?string $transaction_time;
    public ?string $order_id;
    public ?string $fraud_status;
    public ?string $signature_key;
    public ?string $status_code;
    public ?string $gross_amount;
    public ?string $payment_type;
    public ?string $settlement_time;
    public ?array $raw_request;
}
