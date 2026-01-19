<?php

namespace Sanf\Api\Modules\Payment\Support;

use Sanf\Core\Modules\Payment\Exceptions\MidtransInvalidSignatureException;

final class MidtransHelper
{
    public static function getEnabledPaymentOptions(): array
    {
        $enabledPaymentStr = config('midtrans.enabled_payments');

        if (is_array($enabledPaymentStr)) {
            return array_values(array_filter($enabledPaymentStr, static fn ($payment) => $payment !== ''));
        }

        if (!is_string($enabledPaymentStr) || $enabledPaymentStr === '') {
            return [];
        }

        $payments = array_map(static fn ($payment) => trim($payment), explode(',', $enabledPaymentStr));

        return array_values(array_filter($payments, static fn ($payment) => $payment !== ''));
    }

    public static function validateSignature(string $signature, string $orderId, string $statusCode, string $grossAmount): void
    {
        $serverKey = config('midtrans.server_key');

        if (empty($signature)) {
            return;
        }

        if (empty($orderId) || empty($statusCode) || empty($grossAmount)) {
            throw new MidtransInvalidSignatureException();
        }

        $hashed = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if (!hash_equals($hashed, $signature)) {
            throw new MidtransInvalidSignatureException();
        }
    }
}
