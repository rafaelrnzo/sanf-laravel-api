<?php

namespace Sanf\Integration\Modules\Midtrans;

use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;
use Sanf\Integration\Modules\Midtrans\Payloads\CreateSnapTransactionPayload;
use Sanf\Integration\Modules\Midtrans\Responses\CreateSnapTransactionResponse;

class MidtransClient
{
    public function __construct()
    {
        MidtransConfig::$serverKey = config('midtrans.server_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized = config('midtrans.is_sanitized');
        MidtransConfig::$is3ds = config('midtrans.is_3ds');
    }

    public function createSnapTransaction(CreateSnapTransactionPayload $payload)
    {
        $snap = Snap::createTransaction($payload->toArray());

        return new CreateSnapTransactionResponse((array) $snap);
    }
}
