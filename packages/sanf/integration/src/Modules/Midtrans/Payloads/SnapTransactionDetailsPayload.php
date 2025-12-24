<?php

namespace Sanf\Integration\Modules\Midtrans\Payloads;

use Spatie\DataTransferObject\DataTransferObject;

class SnapTransactionDetailsPayload extends DataTransferObject
{
    public string $order_id;
    public int $gross_amount;
}
