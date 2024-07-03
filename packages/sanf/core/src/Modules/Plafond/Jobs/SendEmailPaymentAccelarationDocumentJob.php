<?php

namespace Sanf\Core\Modules\Plafond\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Sanf\Core\Mail\MailLayout2Columns;

class SendEmailPaymentAccelarationDocumentJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $recipient;
    protected $payload;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($recipient, $payload)
    {
        $this->recipient = $recipient;
        $this->payload = $payload;
    }

    public function handle()
    {
        $content = [
            'No Plafond' => $this->payload->plafondId,
            'Perusahaan' => $this->payload->company,
        ];

        $adminMail = config('sanf-mobile.mail_to_admin');
        $reportUrl = "mailto:{$adminMail}?subject=Laporan Pengajuan Surat Percepatan Pencairan Plafon";
        $mailable = (new MailLayout2Columns())
            ->subject('Pengajuan Surat Percepatan Pencairan Plafon')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->greeting(__('Halo :name!', ['name' => $this->payload->company]))
            ->line(__(
                '<blockquote style="margin: 0 0;font-size: 16px; line-height: 150%;">
                    Terdapat permintaan Pengajuan Surat Percepatan Pencairan Plafond, berikut kami lampirkan dokumennya untuk Anda.
                </blockquote>
            '
            ))
            ->writeContent($content)
            ->generateSeparator([
                ['joinToIndex' => 0, 'html' => '<hr style="border: 1px solid rgba(3, 37, 126, 0.08);">'],
            ])
            ->line(
                __('Email ini dibuat secara otomatis mohon tidak membalas email ini, jika terdapat keluhan silahkan hubungi Sanf Customer Service')
            )
            ->lineWithUrl(
                __('. Jika Anda merasa tidak membuat request tersebut mohon abaikan email ini atau anda dapat'),
                [__('Laporkan email ini'), $reportUrl]
            )
            ->attach($this->payload->tempFile, [
                'as' => "Surat-Percepatan-Plafond:{$this->payload->plafondId}.pdf",
                'mime' => 'application/pdf',
            ]);

        return Mail::to([$this->recipient, 'diar@nusantarabetastudio.com', 'muflih@nusantarabetastudio.com'])->send($mailable);
    }
}
