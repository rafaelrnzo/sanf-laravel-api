<?php

namespace Sanf\Core\Modules\StandbyFinancing\Services;

use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class StandbyFinancingBankAccountService
{
    private SanfCoreApiClient $coreClient;

    public function __construct(SanfCoreApiClient $coreClient)
    {
        $this->coreClient = $coreClient;
    }

    public function listForCustomer(string $customerId): array
    {
        $response = $this->coreClient->getUserBankAccount($customerId);

        return array_map(function ($account) use ($customerId) {
            $account = (array) $account;

            return [
                'cust_id' => $customerId,
                'bank_id' => (string) ($account['bank_id'] ?? $account['ID'] ?? ''),
                'owner' => (string) ($account['owner'] ?? $account['OWNER'] ?? ''),
                'provider' => (string) ($account['provider'] ?? $account['PROVIDER'] ?? ''),
                'account_number' => (string) ($account['account_number'] ?? $account['ACCOUNT_NUMBER'] ?? ''),
                'is_default' => (string) ($account['is_default'] ?? $account['IS_DEFAULT'] ?? 'N'),
            ];
        }, $response['data'] ?? []);
    }
}
