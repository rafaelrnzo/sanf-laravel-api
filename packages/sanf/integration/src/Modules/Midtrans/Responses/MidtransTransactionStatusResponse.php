<?php

namespace Sanf\Integration\Modules\Midtrans\Responses;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class MidtransTransactionStatusResponse extends FlexibleDataTransferObject
{
    public ?string $transaction_id;
    public ?string $order_id;
    public ?string $gross_amount;
    public ?string $payment_type;
    public ?string $transaction_status;
    public ?string $transaction_time;
    public ?string $fraud_status;
    public ?string $settlement_time;
    public array $raw = [];
}
