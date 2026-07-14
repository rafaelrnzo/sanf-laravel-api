<?php

namespace Sanf\Core\Modules\StandbyFinancing\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Sanf\Core\Mail\MailLayout2Columns;
use Sanf\Core\Modules\StandbyFinancing\Models\SbfPengajuanModel;
use Sanf\Core\Modules\StandbyFinancing\Services\SbfPengajuanBankAccountSyncer;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;
use Sanf\Integration\Modules\StandbyFinancing\SanfApiService;

class SendSbfStatusChangedEmailJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 3;

    public $backoff = 30;

    public function __construct(
        protected array $data,
    ) {
    }

    public function handle()
    {
        $recapId = $this->data['recap_id_b2b'] ?? null;
        $custId = $this->data['cust_id'] ?? null;
        $clientXid = $this->data['client_xid'] ?? null;

        $coreData = $this->syncFromCore($recapId, $custId);

        $profile = $this->resolveProfile($custId ?? $clientXid);
        $email = $profile['email'] ?? null;

        if (empty($email)) {
            Log::warning('Skipping standby financing status email: recipient email not found', [
                'cust_id' => $custId,
                'client_xid' => $clientXid,
                'recap_id_b2b' => $recapId,
            ]);

            return;
        }

        $name = $profile['name'] ?? 'Pelanggan';
        $companyName = $profile['company'] ?? '-';

        $statusMessage = $this->statusMessage($this->data['status'] ?? null);

        $date = $coreData['date_recap'] ?? $coreData['period_start'] ?? $this->data['period_start'] ?? null;
        $invoiceCount = $coreData['total_invoice'] ?? $this->data['total_invoice_count'] ?? null;
        $totalAmount = $coreData['total_amount'] ?? $this->data['total_amount'] ?? null;

        $content = [
            'Tanggal Pengajuan' => $this->formatDate($date),
            'Nama Perusahaan (Bowheer)' => $companyName,
            'Nomor Pengajuan' => $recapId ?? '-',
            'Jumlah Invoice' => $invoiceCount !== null ? $invoiceCount : '-',
            'Total Nilai Invoice' => $this->formatCurrency($totalAmount),
        ];

        $adminMail = config('sanf-mobile.mail_to_admin');
        $reportUrl = "mailto:{$adminMail}?subject=Laporan Pengajuan Standby Financing";
        $customerServiceMail = config('sanf-mobile.mail_to.customer_service');
        $customerServiceUrl = "mailto:{$customerServiceMail}?subject=Keluhan Pengajuan Standby Financing";

        $mailable = (new MailLayout2Columns())
            ->subject('Update Status Pengajuan Standby Financing')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->greeting("Halo {$name}!")
            ->line("<blockquote style=\"margin: 0 0;font-size: 16px; line-height: 150%;\">{$statusMessage}</blockquote>")
            ->writeContent($content)
            ->generateSeparator([
                ['joinToIndex' => 1, 'html' => '<hr style="border: 1px solid rgba(3, 37, 126, 0.08);">'],
            ])
            ->lineWithUrl(
                'Email ini dibuat secara otomatis mohon tidak membalas email ini, jika terdapat keluhan silahkan hubungi',
                ['SANF Care', $customerServiceUrl]
            )
            ->lineWithUrl(
                '. Jika Anda merasa tidak membuat request tersebut mohon abaikan email ini atau anda dapat',
                ['Laporkan email ini', $reportUrl]
            );

        return Mail::to($email)->send($mailable);
    }

    private function syncFromCore(?string $recapId, ?string $custId): array
    {
        if (empty($recapId)) {
            return [];
        }

        try {
            $service = app(SanfApiService::class)->setUser($custId);
            $detail = $service->getDetailPencairan($recapId);

            $pengajuan = SbfPengajuanModel::where('core_recap_id', $recapId)->first();

            if ($pengajuan && !empty($detail)) {
                $bankAccountSyncer = app(SbfPengajuanBankAccountSyncer::class);
                $bankAccounts = $bankAccountSyncer->normalizeWebhookData($detail);
                $bankAccountSyncer->sync($pengajuan, $bankAccounts, 'core-detail');
                $bank = $bankAccountSyncer->first($bankAccounts) ?? [];

                $pengajuan->update([
                    'period_start' => $detail['period_start'] ?? $pengajuan->period_start,
                    'period_end' => $detail['period_end'] ?? $pengajuan->period_end,
                    'tenor' => $detail['tenor'] ?? $pengajuan->tenor,
                    'total_invoice_count' => $detail['total_invoice'] ?? $pengajuan->total_invoice_count,
                    'total_amount' => $detail['total_amount'] ?? $pengajuan->total_amount,
                    'bank_id' => $bank['bank_id'] ?? $pengajuan->bank_id,
                    'bank_provider' => $bank['bank_provider'] ?? $pengajuan->bank_provider,
                    'bank_owner' => $bank['bank_owner'] ?? $pengajuan->bank_owner,
                    'bank_account_number' => $bank['bank_account_number'] ?? $pengajuan->bank_account_number,
                    'supplier_payload' => $detail['supplier'] ?? ($detail['suppliers'] ?? $pengajuan->supplier_payload),
                    'invoice_document' => $detail['invoice_document'] ?? $pengajuan->invoice_document,
                    'spt_dokument' => $detail['spt_dokuments'] ?? $pengajuan->spt_dokument,
                    'supporting_dokuments' => $detail['supporting_dokuments'] ?? $pengajuan->supporting_dokuments,
                    'core_response' => $detail,
                ]);
            }

            return $detail;
        } catch (\Throwable $e) {
            Log::warning('Failed to sync SBF data from Core: ' . $e->getMessage(), [
                'recap_id_b2b' => $recapId,
                'cust_id' => $custId,
            ]);

            return [];
        }
    }

    private function statusMessage(?string $code): string
    {
        $generic = 'Pengajuan anjak piutang anda sedang diproses, berikut kami lampirkan ringkasan pengajuan Anda.';

        if ($code === null || $code === '') {
            return $generic;
        }

        return match (strtoupper(trim($code))) {
            'C' => 'Pengajuan Anda telah dikonfirmasi dan sedang diproses.',
            '1' => 'Pengajuan Anda dinyatakan valid dan sedang diproses.',
            '2' => 'Pengajuan Anda dinyatakan tidak valid. Mohon periksa kembali data pengajuan Anda.',
            'X' => 'Pengajuan Anda ditolak.',
            '0' => 'Status pengajuan Anda sedang ditinjau.',
            default => $this->unknownStatusMessage($code, $generic),
        };
    }

    private function unknownStatusMessage(string $code, string $fallback): string
    {
        Log::warning('Unmapped standby financing status code in webhook email', [
            'status' => $code,
            'recap_id_b2b' => $this->data['recap_id_b2b'] ?? null,
        ]);

        return $fallback;
    }

    private function resolveProfile(?string $customerId): ?array
    {
        if (empty($customerId)) {
            return null;
        }

        try {
            $response = app(SanfCoreApiClient::class)->findCustomerById($customerId);

            return [
                'email' => $response['EMAIL_ADDR'] ?? $response['EMAIL_STAFF'] ?? null,
                'name' => $response['PIC_NAME'] ?? $response['IDENTITY_NAME'] ?? null,
                'company' => $response['IDENTITY_NAME'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::warning('Failed to resolve customer profile for SBF status email: ' . $e->getMessage(), [
                'customer_id' => $customerId,
            ]);

            return null;
        }
    }

    private function formatDate($date): string
    {
        if (empty($date)) {
            return '-';
        }

        try {
            return Carbon::parse($date)->format('d F Y');
        } catch (\Exception $e) {
            return (string) $date;
        }
    }

    private function formatCurrency($amount): string
    {
        if ($amount === null || $amount === '') {
            return '-';
        }

        return 'Rp. ' . number_format((float) $amount, 0, ',', '.');
    }
}
