<?php

namespace Sanf\Dashboard\Modules\User\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Sanf\Core\Mail\MailLayout2Columns;

class SendEmailActivationAccountBindingByCoreJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $recipients;
    protected $payload;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($recipients, $payload)
    {
        $this->recipients = $recipients;
        $this->payload = $payload;
    }

    public function handle()
    {
        $fullName = $this->payload['fullName'];
        $verifyUrl = $this->payload['verifyUrl'];
        $expireInDays = $this->payload['expireInDays'];
        unset($this->payload['fullName']);

        $adminMail = config('sanf-mobile.mail_to_admin');
        $reportUrl = "mailto:{$adminMail}?subject=Laporan Aktivasi Akun Web Partner";
        $customerServiceMail = config('sanf-mobile.mail_to.customer_service');
        $customerServiceUrl = "mailto:{$customerServiceMail}?subject=Keluhan Aktivasi Akun Web Partner";
        $mailable = (new MailLayout2Columns())
            ->subject('Aktivasi Akun Web Partner')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->greeting(__('<strong>Selamat datang di ' . config('web-partner.app_name') . '</strong><br><br>Hi <strong>:name</strong>!', ['name' => $fullName]))
            ->line(__(
                '<blockquote style="margin: 0 0;font-size: 16px; line-height: 150%;">
                    Email anda telah didaftarkan kedalam akun SANFIND Web Partner Oleh PT Surya Artha Nusantara Finance. Silahkan aktifkan akun Anda dengan mengklik tombol dibawah
                </blockquote>
            '
            ))
            ->action(__('Activate my acocunt'), $verifyUrl)
            ->lineWithUrl(
                '<div style="color: #232227; font-size: 13px; font-family: Questrial; font-weight: 500; line-height: 15px; word-wrap: break-word">Atau Anda dapat klik link berikut untuk memverifikasi akun</div>',
                [$verifyUrl, $verifyUrl]
            )
            ->lineWithUrl(
                '<div style="color: #999BAC; font-size: 13px; font-family: Questrial; font-weight: 500; line-height: 16px; word-wrap: break-word">Note: Tautan berlaku ' . $expireInDays . ' hari sejak pengiriman</div>',
                ['', '']
            )
            ->lineWithUrl(
                __('<br><br>Email ini dibuat secara otomatis mohon tidak membalas email ini, jika terdapat keluhan silahkan hubungi'),
                [$customerServiceMail, $customerServiceUrl]
            )
            ->lineWithUrl(
                __('. Jika Anda merasa tidak membuat request tersebut mohon abaikan email ini atau anda dapat'),
                [__('laporkan email ini'), $reportUrl]
            );

        return Mail::to($this->recipients)->send($mailable);
    }
}
