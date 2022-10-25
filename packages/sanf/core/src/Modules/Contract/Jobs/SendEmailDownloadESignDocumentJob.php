<?php

namespace Sanf\Core\Modules\Contract\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use NbsPhp\Core\Mail\BaseMail;

class SendEmailDownloadESignDocumentJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected string $recipient;
    protected array $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($recipient, $data)
    {
        $this->recipient = $recipient;
        $this->data = $data;
    }

    public function handle()
    {
        $adminMail = config('sanf-mobile.mail_to_admin');
        $reportUrl = "mailto:{$adminMail}?subject=Laporan Dokumen Kontrak";
        $email = (new BaseMail())
            ->subject('Dokumen Kontrak')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->line(__(
                'Halo ' . $this->data['fullName'] . '!.
                <br />
                <blockquote style="margin: 0 3em;font-size: 16px; line-height: 150%;">
                    Berikut kami lampirkan dokumen yang telah selesai ditanda tangani.
                </blockquote>
            '))
            ->line(
                __('Email ini dibuat secara otomatis mohon tidak membalas email ini, jika terdapat keluhan silahkan hubungi Sanf Customer Service')
            )
            ->lineWithUrl(
                __('. Jika Anda merasa tidak membuat request tersebut mohon abaikan email ini atau anda dapat'),
                [__('Laporkan email ini'), $reportUrl]
            );

        $email->attachFromStorage($this->data['path'], $this->data['documentName']);

        return Mail::to($this->recipient)->send($email);
    }

}