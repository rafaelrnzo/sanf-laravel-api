<?php

namespace Sanf\Core\Modules\User\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use NbsPhp\Core\Mail\BaseMail;

class SendRequestDeletionAccountForUserNotification implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    protected $emailRecipients;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $emailRecipients)
    {
        $this->data = $data;
        $this->emailRecipients = $emailRecipients;
    }

    public function handle()
    {
        $adminMail = config('sanf-mobile.mail_to_admin');
        $reportUrl = "mailto:{$adminMail}?subject=Laporan Permintaan Hapus Akun";
        $mail = (new BaseMail())
            ->subject('Permintaan Hapus Akun')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->greeting("Halo {$this->data['name']}")
            ->line(
                "Pengguna atas nama <span class='text-bold'>{$this->data['name']}</span> saat ini {$this->data['createdAt']} WIB telah mengajukan  permintaan untuk Hapus Akun. Sistem akan secara otomatis menghapus akun jika pengguna tidak login ke aplikasi terhitung sejak tanggal {$this->data['restoreExpiredAt']}."
            )
            ->lineWithUrl(
                __('Jika Anda merasa tidak membuat request tersebut mohon abaikan email ini atau anda dapat'),
                [__('laporkan email ini'), $reportUrl]
            );

        return Mail::to($this->emailRecipients)->send($mail);
    }
}
