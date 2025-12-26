<?php

namespace Sanf\Api\Modules\Payment\Support;

use Illuminate\Support\Arr;
use Sanf\Integration\Modules\Midtrans\Enums\MidtransPaymentTypeEnum;

final class MidtransPaymentMethodResolver
{
    /**
     * Resolve payment method details (VA/bank metadata) from a Midtrans transaction.
     * @param array|null $transactionResponse Response from Midtrans transaction status
     */
    public static function resolve(?array $transactionResponse): MidtransPaymentMethod
    {
        $result = self::emptyResult();

        if ($transactionResponse === null) {
            return $result;
        }

        $midtransRaw = $transactionResponse ?? [];
        $result->type = $type = $midtransRaw['payment_type'];

        // Mandiri echannel (bill payment)
        if ($type === MidtransPaymentTypeEnum::ECHANNEL) {
            $result->provider = 'mandiri';
            $result->biller_code = $midtransRaw['biller_code'] ?? null;
            $result->bill_key = $midtransRaw['bill_key'] ?? null;
            $result->provider_logo_url = MidtransLogoUrlResolver::resolve($result->provider);

            return $result;
        }

        // General VA
        if (!empty($midtransRaw['va_numbers']) && is_array($midtransRaw['va_numbers'])) {
            $virtualAccount = Arr::first($midtransRaw['va_numbers']);

            $result->provider = $virtualAccount['bank'] ?? null;
            $result->virtual_account_number = $virtualAccount['va_number'] ?? null;
            $result->provider_logo_url = MidtransLogoUrlResolver::resolve($result->provider);

            return $result;
        }

        // Permata VA
        if (!empty($midtransRaw['permata_va_number'])) {
            $result->provider = $midtransRaw['bank'] ?? 'permata';
            $result->virtual_account_number = $midtransRaw['permata_va_number'];
            $result->provider_logo_url = MidtransLogoUrlResolver::resolve($result->provider);

            return $result;
        }

        // BCA VA
        if (!empty($midtransRaw['bca_va_number'])) {
            $result->provider = $midtransRaw['bank'] ?? 'permata';
            $result->virtual_account_number = $midtransRaw['bca_va_number'];
            $result->provider_logo_url = MidtransLogoUrlResolver::resolve($result->provider);

            return $result;
        }

        return $result;
    }

    public static function emptyResult(): MidtransPaymentMethod
    {
        return MidtransPaymentMethod::empty();
    }
}
