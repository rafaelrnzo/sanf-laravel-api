<?php

namespace Sanf\Api\Modules\Scanina\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Sanf\Core\Mail\MailLayout2Columns;

class SendEmailProductAddToCartForUserJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected array $data;
    protected array $recipient;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data, array $recipient)
    {
        $this->data = $data;
        $this->recipient = $recipient;
    }

    public function handle()
    {
        $adminMail = config('sanf-mobile.mail_to_admin');
        $reportUrl = "mailto:{$adminMail}?subject=Laporan Pengajuan Pembiayaan Kendaraan Scanina.com";

        $mailable = (new MailLayout2Columns())
            ->subject('Pengajuan Pembiayaan Kendaraan Scanina.com')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->greeting(__("Halo {$this->data['fullName']}!"))
            ->line(__('Pengajuan Pembiayaan Anda sedang Diproses oleh Tim Kami, berikut kami lampirkan ringkasan pembiayaan kendaraan Anda.'))
            ->writeContent($this->data['content'])
            ->generateSeparator([
                ['joinToIndex' => 2, 'html' => '<hr style="border: 1px solid rgba(3, 37, 126, 0.08); margin: 5px 0;">'],
            ])
            ->line(
                __('Email ini dibuat secara otomatis mohon tidak membalas email ini, jika terdapat keluhan silahkan hubungi Sanf Customer Service')
            )
            ->lineWithUrl(
                __('. Jika Anda merasa tidak membuat request tersebut mohon abaikan email ini atau anda dapat'),
                [__('Laporkan email ini'), $reportUrl]
            );

        return Mail::to($this->recipient)->send($mailable);
    }
}
