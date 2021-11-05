<?php


namespace Sanf\Core\Modules\Plafond;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Sanf\Core\Mail\MailLayout2Columns;

class SendEmailPlafondJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $plafond;

    protected $emailRecipients;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($plafond, $emailRecipients)
    {
        $this->plafond = $plafond;
        $this->emailRecipients = $emailRecipients;
    }

    public function handle()
    {

        $plafondResult = (new MailLayout2Columns())
            ->subject('Pengajuan PLAFON Baru')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->greeting(__('Halo Bambang!'))
            ->line(__(
                '<blockquote style="margin: 0 0;font-size: 16px; line-height: 150%;">
                    Pengajuan Plafon Sedang dalam proses oleh tim kami, berikut kami lampirkan ringkasan pengajuan Plafon Anda.
                </blockquote>
            '))
            ->writeContent($this->plafond)
            ->generateSeparator([
                [ 'joinToIndex' => 1, 'html' => '<hr style="border: 1px solid rgba(3, 37, 126, 0.08);">' ],
            ])
            ->lineWithUrl(
                __('Email ini dibuat secara otomatis mohon tidak membalas email ini, jika terdapat keluhan silahkan hubungi'),
                [__('Sanf Customer Service'), '#']
            )
            ->lineWithUrl(
                __('. Jika Anda merasa tidak membuat request tersebut mohon abaikan email ini atau anda dapat'),
                [__('Laporkan email ini'), '#']
            );

        return Mail::to($this->emailRecipients)->send($plafondResult);
    }
}
