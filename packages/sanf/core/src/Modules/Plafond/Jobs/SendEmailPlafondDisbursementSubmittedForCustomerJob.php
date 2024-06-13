<?php

namespace Sanf\Core\Modules\Plafond\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Sanf\Core\Mail\MailLayout2Columns;

class SendEmailPlafondDisbursementSubmittedForCustomerJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $data;
    protected $recipients;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $recipients)
    {
        $this->data = $data;
        $this->recipients = $recipients;
    }

    public function handle()
    {
        $fullName = $this->data['to'];
        unset($this->data['to']);

        $adminMail = config('sanf-mobile.mail_to_admin');
        $reportUrl = "mailto:{$adminMail}?subject=Laporan Pengajuan Pencairan Plafon";
        $mailable = (new MailLayout2Columns())
            ->subject('Pengajuan Pencairan Plafon')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->greeting(__('Hi :name!', ['name' => $fullName]))
            ->line(__(
                '<blockquote style="margin: 0 0;font-size: 16px; line-height: 150%;">
                Kamu memiliki permohonan percepatan pembayaran yang perlu direview. Berikut adalah rincian nya.
                </blockquote>'
            ))
            ->line(__(
                '<blockquote style="margin: 0 0;font-size: 16px; line-height: 150%;">
                Detail Anjak Piutang
                </blockquote>'
            ))
            ->writeContent($this->data)
            ->generateSeparator([
                ['joinToIndex' => 4, 'html' => '<hr style="border: 1px solid rgba(3, 37, 126, 0.08);">'],
            ])
            ->line(
                __('Email ini dibuat secara otomatis mohon tidak membalas email ini, jika terdapat keluhan silahkan hubungi Sanf Customer Service')
            )
            ->lineWithUrl(
                __('. Jika Anda merasa tidak membuat request tersebut mohon abaikan email ini atau anda dapat'),
                [__('Laporkan email ini'), $reportUrl]
            );

        return Mail::to($this->recipients)->send($mailable);
    }
}
