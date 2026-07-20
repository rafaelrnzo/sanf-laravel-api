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
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class SendSbfSubmittedEmailJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 3;

    public $backoff = 30;

    public function __construct(
        protected array $data,
        protected ?array $recipients = null,
    ) {
    }

    public function handle()
    {
        $profile = $this->resolveProfile($this->data['cust_id'] ?? $this->data['client_xid'] ?? null);
        $recipients = $this->recipients ?: [$profile['email'] ?? null];
        $recipients = array_values(array_filter($recipients));

        if (empty($recipients)) {
            Log::warning('Skipping standby financing submitted email: recipient email not found', [
                'cust_id' => $this->data['cust_id'] ?? null,
                'client_xid' => $this->data['client_xid'] ?? null,
                'recap_id_b2b' => $this->data['recap_id_b2b'] ?? null,
            ]);

            return;
        }

        $name = $this->recipients ? 'Tim SANF' : ($profile['name'] ?? 'Pelanggan');
        $companyName = $profile['company'] ?? '-';

        $content = [
            'Tanggal Pengajuan' => $this->formatDate($this->data['date_recap'] ?? $this->data['period_start'] ?? null),
            'Nama Perusahaan (Bowheer)' => $companyName,
            'Nomor Pengajuan' => $this->data['recap_id_b2b'] ?? '-',
            'Jumlah Invoice' => $this->data['total_invoice_count'] ?? '-',
            'Total Nilai Invoice' => $this->formatCurrency($this->data['total_amount'] ?? null),
        ];

        $adminMail = config('sanf-mobile.mail_to_admin');
        $reportUrl = "mailto:{$adminMail}?subject=Laporan Pengajuan Standby Financing";
        $customerServiceMail = config('sanf-mobile.mail_to.customer_service');
        $customerServiceUrl = "mailto:{$customerServiceMail}?subject=Keluhan Pengajuan Standby Financing";

        $mailable = (new MailLayout2Columns())
            ->subject('Pengajuan Standby Financing')
            ->leftLogo($this->publicAsset('assets/png/sanf-logo-blue.png'))
            ->rightLogo($this->publicAsset('assets/png/sanf-tagline.png'))
            ->banner($this->publicAsset('assets/png/email-verification.png'))
            ->greeting("Halo {$name}!")
            ->line('<blockquote style="margin: 0 0;font-size: 16px; line-height: 150%;">Pengajuan anjak piutang anda sedang diproses, berikut kami lampirkan ringkasan pengajuan Anda.</blockquote>')
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

        return Mail::to($recipients)->send($mailable);
    }

    private function publicAsset(string $path): string
    {
        $baseUrl = rtrim((string) (config('app.asset_url') ?: config('app.url')), '/');

        return $baseUrl . '/' . ltrim($path, '/');
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
            Log::warning('Failed to resolve customer profile for SBF submitted email: ' . $e->getMessage(), [
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
