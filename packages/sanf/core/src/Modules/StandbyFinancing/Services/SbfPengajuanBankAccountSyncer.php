<?php

namespace Sanf\Core\Modules\StandbyFinancing\Services;

use Sanf\Core\Modules\StandbyFinancing\Models\SbfPengajuanBankAccountModel;
use Sanf\Core\Modules\StandbyFinancing\Models\SbfPengajuanModel;

class SbfPengajuanBankAccountSyncer
{
    public function normalizePayload(array $payload): array
    {
        if (!empty($payload['bank_accounts']) && is_array($payload['bank_accounts'])) {
            return $this->normalizeEntries($payload['bank_accounts']);
        }

        if (!empty($payload['bank_account']) && is_array($payload['bank_account'])) {
            return $this->normalizeEntries([$payload['bank_account']]);
        }

        return [];
    }

    public function normalizeWebhookData(array $data): array
    {
        $entries = [];

        foreach ([$data, $data['detail'] ?? []] as $source) {
            if (!is_array($source)) {
                continue;
            }

            foreach (['bank_accounts', 'banks', 'bank_account', 'bank'] as $key) {
                if (!empty($source[$key]) && is_array($source[$key])) {
                    $entries = array_merge($entries, $this->entryList($source[$key]));
                }
            }
        }

        return $this->normalizeEntries($entries);
    }

    public function first(array $bankAccounts): ?array
    {
        $bankAccounts = $this->normalizeEntries($bankAccounts);

        return $bankAccounts[0] ?? null;
    }

    public function sync(SbfPengajuanModel $pengajuan, array $bankAccounts, string $source): void
    {
        foreach ($this->normalizeEntries($bankAccounts) as $bankAccount) {
            SbfPengajuanBankAccountModel::updateOrCreate(
                [
                    'pengajuan_id' => $pengajuan->getKey(),
                    'bank_id' => $bankAccount['bank_id'],
                    'bank_account_number' => $bankAccount['bank_account_number'],
                ],
                [
                    'pengajuan_id' => $pengajuan->getKey(),
                    'bank_id' => $bankAccount['bank_id'],
                    'bank_owner' => $bankAccount['bank_owner'],
                    'bank_provider' => $bankAccount['bank_provider'],
                    'bank_account_number' => $bankAccount['bank_account_number'],
                    'source' => $source,
                ]
            );
        }
    }

    private function normalizeEntries(array $entries): array
    {
        $normalized = [];

        foreach ($this->entryList($entries) as $entry) {
            if (!is_array($entry)) {
                continue;
            }

            $bankAccount = $this->normalizeOne($entry);

            if ($bankAccount === null) {
                continue;
            }

            $key = $bankAccount['bank_id'] . '|' . $bankAccount['bank_account_number'];
            $normalized[$key] = $bankAccount;
        }

        return array_values($normalized);
    }

    private function normalizeOne(array $entry): ?array
    {
        $bankId = $this->stringValue($entry['bank_id'] ?? $entry['id'] ?? '');
        $bankOwner = $this->stringValue($entry['bank_owner'] ?? $entry['owner'] ?? '');
        $bankProvider = $this->stringValue($entry['bank_provider'] ?? $entry['provider'] ?? '');
        $accountNumber = $this->stringValue(
            $entry['bank_account_number']
                ?? $entry['account_number']
                ?? $entry['account_no']
                ?? ''
        );

        if ($bankId === '' && $bankOwner === '' && $bankProvider === '' && $accountNumber === '') {
            return null;
        }

        return [
            'bank_id' => $bankId,
            'bank_owner' => $bankOwner,
            'bank_provider' => $bankProvider,
            'bank_account_number' => $accountNumber,
        ];
    }

    private function entryList(array $entries): array
    {
        return array_is_list($entries) ? $entries : [$entries];
    }

    private function stringValue($value): string
    {
        return trim((string) $value);
    }
}
