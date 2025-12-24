<?php

namespace Sanf\Integration\Modules\Midtrans;

use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;
use Midtrans\Transaction;
use Sanf\Integration\Modules\Midtrans\Payloads\CreateSnapTransactionPayload;
use Sanf\Integration\Modules\Midtrans\Responses\CreateSnapTransactionResponse;
use Sanf\Integration\Modules\Midtrans\Responses\MidtransTransactionStatusResponse;
use Symfony\Component\HttpFoundation\Response;

class MidtransClient
{
    public const TIMEZONE = 'Asia/Jakarta';

    public function __construct()
    {
        MidtransConfig::$serverKey = config('midtrans.server_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized = config('midtrans.is_sanitized');
        MidtransConfig::$is3ds = config('midtrans.is_3ds');
    }

    public function createSnapTransaction(CreateSnapTransactionPayload $payload): CreateSnapTransactionResponse
    {
        $snap = Snap::createTransaction($payload->toArray());

        return new CreateSnapTransactionResponse((array) $snap);
    }

    public function getTransactionStatus(string $orderId): ?MidtransTransactionStatusResponse
    {
        try {
            $response = Transaction::status($orderId);
        } catch (\Throwable $th) {
            if ($th->getCode() === Response::HTTP_NOT_FOUND) {
                return null;
            }

            throw $th;
        }

        return new MidtransTransactionStatusResponse(array_merge(
            (array) $response,
            ['raw' => (array) $response]
        ));
    }

    public function cancelTransaction(string $orderId): void
    {
        Transaction::cancel($orderId);
    }
}
