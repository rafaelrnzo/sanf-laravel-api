<?php

namespace Sanf\Api\Modules\Payment\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Api\Modules\Payment\Support\MidtransLogoUrlResolver;
use Sanf\Core\Modules\Payment\Models\PaymentModel;
use Sanf\Integration\Modules\Midtrans\Enums\MidtransPaymentTypeEnum;

final class PaymentMethodTransformer extends TransformerAbstract
{
    public function transform(PaymentModel $model)
    {
        $midtransTransaction = $model->activeMidtransTransaction;

        $result = [
            'type' => null,
            'provider' => null,
            'virtual_account_number' => null,
            'biller_code' => null,
            'bill_key' => null,
            'provider_logo_url' => null,
        ];

        if (!$midtransTransaction) {
            return $result;
        }

        $result['type'] = $type = $midtransTransaction->payment_type;

        $midtransRaw = $midtransTransaction->raw_response;

        // Mandiri echannel (bill payment)
        if ($type === MidtransPaymentTypeEnum::ECHANNEL) {
            $result['provider'] = 'mandiri';
            $result['biller_code'] = $midtransRaw['biller_code'];
            $result['bill_key'] = $midtransRaw['bill_key'];
            $result['provider_logo_url'] = MidtransLogoUrlResolver::resolve($result['provider']);

            return $result;
        }

        // General VA
        if (!empty($midtransRaw['va_numbers']) && is_array($midtransRaw['va_numbers'])) {
            $virtualAccounts = collect($midtransRaw['va_numbers'] ?? []);
            $virtualAccount = $virtualAccounts->first();

            $result['provider'] = $virtualAccount['bank'] ?? null;
            $result['virtual_account_number'] = $virtualAccount['va_number'] ?? null;
            $result['provider_logo_url'] = MidtransLogoUrlResolver::resolve($result['provider']);

            return $result;
        }

        // Permata VA
        if (!empty($midtransRaw['permata_va_number'])) {
            $result['provider'] = $midtransRaw['bank'] ?? 'permata';
            $result['virtual_account_number'] = $midtransRaw['permata_va_number'];
            $result['provider_logo_url'] = MidtransLogoUrlResolver::resolve($result['provider']);

            return $result;
        }

        // BCA VA
        if (!empty($midtransRaw['bca_va_number'])) {
            $result['provider'] = $midtransRaw['bank'] ?? 'permata';
            $result['virtual_account_number'] = $midtransRaw['bca_va_number'];
            $result['provider_logo_url'] = MidtransLogoUrlResolver::resolve($result['provider']);

            return $result;
        }

        return $result;
    }
}
