<?php

namespace Sanf\Core\Modules\User\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use NbsPhp\Core\Mail\BaseMail;

class SendApprovalRequestDeletionAccountNotification implements ShouldQueue
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
            ->subject('Pemberitahuan Hapus Akun')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->greeting("Halo <span class='text-bold'>{$this->data['name']}</span>")
            ->line("Permintaan penghapusan akun SANFIND berhasil disetujui.")
            ->line("Jika Anda merasa tidak membuat request tersebut abaikan email ini atau Anda dapat <span class='text-blue text-bold'>laporkan email ini</span>");

        return Mail::to($this->emailRecipients)->send($mail);
    }
}
