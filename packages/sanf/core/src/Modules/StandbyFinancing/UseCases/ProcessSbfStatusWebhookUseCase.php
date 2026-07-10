<?php

namespace Sanf\Core\Modules\StandbyFinancing\UseCases;

use Illuminate\Support\Carbon;
use Sanf\Core\Modules\Log\Enums\WebhookLogKeyEnum;
use Sanf\Core\Modules\Log\Models\WebhookLogModel;
use Sanf\Core\Modules\StandbyFinancing\Jobs\SendSbfStatusChangedEmailJob;
use Sanf\Core\Modules\StandbyFinancing\Models\SbfPengajuanModel;

class ProcessSbfStatusWebhookUseCase
{
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
            $pengajuan->update([
                'core_status' => $statusCode,
                'core_message' => $this->statusMessage($statusCode),
                'core_response' => $payload,
            ]);
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
