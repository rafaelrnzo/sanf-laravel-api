<?php

namespace Sanf\Integration\Modules\StandbyFinancing\Payloads;

use Spatie\DataTransferObject\DataTransferObject;

class SbfSubmitPengajuanPayload extends DataTransferObject
{
    public string $no_plafond;
    public string $period_start;
    public string $period_end;
    public int $tenor;
    public array $supplier;
    public SbfBankAccountPayload $bank_account;
    public array $invoice_document;

    public $spt_dokuments;

    public array $supporting_dokuments;

    public static function fromValidated(array $payload): self
    {
        $bank = $payload['bank_account'] ?? ($payload['bank_accounts'][0] ?? []);
        $totalAmount = array_sum(array_column($payload['supplier'] ?? [], 'total_amount'));

        return new self([
            'no_plafond' => (string) ($payload['no_plafond'] ?? ''),
            'period_start' => (string) ($payload['period_start'] ?? ''),
            'period_end' => (string) ($payload['period_end'] ?? ''),
            'tenor' => (int) ($payload['tenor'] ?? 0),
            'supplier' => $payload['supplier'] ?? [],
            'bank_account' => new SbfBankAccountPayload([
                'bank_id' => (string) ($bank['bank_id'] ?? ''),
                'owner' => (string) ($bank['bank_owner'] ?? $bank['owner'] ?? ''),
                'provider' => (string) ($bank['bank_provider'] ?? $bank['provider'] ?? ''),
                'account_number' => (string) ($bank['bank_account_number'] ?? $bank['account_number'] ?? ''),
                'total_amount' => (string) $totalAmount,
            ]),
            'invoice_document' => $payload['invoice_document'] ?? [],
            'spt_dokuments' => $payload['spt_dokuments'] ?? null,
            'supporting_dokuments' => $payload['supporting_dokuments'] ?? [],
        ]);
    }
}
