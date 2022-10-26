<?php

namespace Sanf\Core\Modules\User\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use NbsPhp\Core\Mail\BaseMail;

class SendRequestDeletionAccountForAdminNotification implements ShouldQueue
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
        $mail = (new BaseMail())
            ->subject('Permintaan Hapus Akun')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->greeting("Halo Admin SANFIND!")
            ->line(
                "Pengguna atas nama <span class='text-bold'>{$this->data['name']}</span> saat ini {$this->data['createdAt']} WIB telah mengajukan  permintaan untuk Hapus Akun."
            );

        return Mail::to($this->emailRecipients)->send($mail);
    }
}
