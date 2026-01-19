<?php

namespace Sanf\Api\Modules\Payment\Support;

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
}
