<?php

namespace Sanf\Core\Modules\StandbyFinancing\UseCases;

use Illuminate\Support\Carbon;
use Sanf\Core\Modules\Log\Enums\WebhookLogKeyEnum;
use Sanf\Core\Modules\Log\Models\WebhookLogModel;
use Sanf\Core\Modules\StandbyFinancing\Jobs\SendSbfStatusChangedEmailJob;
use Sanf\Core\Modules\StandbyFinancing\Models\SbfPengajuanModel;
use Sanf\Core\Modules\StandbyFinancing\Services\SbfPengajuanBankAccountSyncer;

class ProcessSbfStatusWebhookUseCase
{
    private SbfPengajuanBankAccountSyncer $bankAccountSyncer;

    public function __construct(?SbfPengajuanBankAccountSyncer $bankAccountSyncer = null)
    {
        $this->bankAccountSyncer = $bankAccountSyncer ?? new SbfPengajuanBankAccountSyncer();
    }

    public function handle(array $payload): string
    {
        $eventId = $payload['event_id'];
        $data = $payload['data'] ?? [];

        $webhookLog = WebhookLogModel::firstOrCreate(
            ['xid' => $eventId],
            [
                'key' => WebhookLogKeyEnum::SBF_STATUS,
                'reference_id' => $data['recap_id_b2b'] ?? '',
                'payload' => $payload,
                'received_at' => (string) Carbon::now(),
                'processed_at' => null,
            ]
        );

        if (!$webhookLog->wasRecentlyCreated) {
            return 'duplicate';
        }

        $pengajuan = SbfPengajuanModel::where('core_recap_id', $data['recap_id_b2b'] ?? null)->first();

        $statusCode = $data['status'] ?? null;

        if ($pengajuan) {
            $bankAccounts = $this->bankAccountSyncer->normalizeWebhookData($data);
            $pengajuan->update($this->submissionAttributes($payload, $data, $bankAccounts));

            $this->bankAccountSyncer->sync($pengajuan, $bankAccounts, 'webhook');
        }

        $webhookLog->update(['processed_at' => (string) Carbon::now()]);

        dispatch(new SendSbfStatusChangedEmailJob([
            'status' => $statusCode,
            'recap_id_b2b' => $data['recap_id_b2b'] ?? null,
            'client_xid' => $data['client_xid'] ?? null,
            'cust_id' => $pengajuan ? $pengajuan->cust_id : null,
            'total_invoice_count' => $pengajuan ? $pengajuan->total_invoice_count : null,
            'total_amount' => $pengajuan ? $pengajuan->total_amount : null,
            'period_start' => $pengajuan ? $pengajuan->period_start : null,
        ]));

        return 'processed';
    }

    private function submissionAttributes(
        array $payload,
        array $data,
        array $bankAccounts
    ): array
    {
        $detail = $data['detail'] ?? [];
        $attributes = [
            'local_status' => 'submitted',
            'core_status' => $data['status'] ?? null,
            'core_message' => $this->statusMessage($data['status'] ?? null),
            'core_response' => $payload,
        ];

        if (is_array($detail) && !empty($detail)) {
            $attributes = array_merge($attributes, $this->detailAttributes($detail));
        }

        if ($bankAccount = $this->bankAccountSyncer->first($bankAccounts)) {
            $attributes = array_merge($attributes, [
                'bank_id' => $bankAccount['bank_id'],
                'bank_owner' => $bankAccount['bank_owner'],
                'bank_provider' => $bankAccount['bank_provider'],
                'bank_account_number' => $bankAccount['bank_account_number'],
            ]);
        }

        return $attributes;
    }

    private function detailAttributes(array $detail): array
    {
        $attributes = [];
        $map = [
            'no_plafond' => ['no_plafond', 'noplafond'],
            'period_start' => ['period_start'],
            'period_end' => ['period_end'],
            'tenor' => ['tenor'],
            'total_invoice_count' => ['total_invoice_count', 'total_invoice'],
            'total_amount' => ['total_amount'],
            'supplier_payload' => ['supplier', 'suppliers'],
            'invoice_document' => ['invoice_document'],
            'spt_dokument' => ['spt_dokuments', 'spt_dokument'],
            'supporting_dokuments' => ['supporting_dokuments'],
        ];

        foreach ($map as $attribute => $keys) {
            foreach ($keys as $key) {
                if (array_key_exists($key, $detail) && $detail[$key] !== null) {
                    $attributes[$attribute] = $detail[$key];
                    break;
                }
            }
        }

        return $attributes;
    }

    private function statusMessage(?string $code): string
    {
        if ($code === null || $code === '') {
            return 'Status pengajuan telah diperbarui.';
        }

        return match (strtoupper(trim($code))) {
            'C' => 'Pengajuan Anda telah dikonfirmasi dan sedang diproses.',
            '1' => 'Pengajuan Anda dinyatakan valid dan sedang diproses.',
            '2' => 'Pengajuan Anda dinyatakan tidak valid. Mohon periksa kembali data pengajuan Anda.',
            'X' => 'Pengajuan Anda ditolak.',
            '0' => 'Status pengajuan Anda sedang ditinjau.',
            default => 'Status pengajuan telah diperbarui ke: ' . $code,
        };
    }
}
