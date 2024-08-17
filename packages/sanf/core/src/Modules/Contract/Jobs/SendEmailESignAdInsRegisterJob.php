<?php

namespace Sanf\Core\Modules\Contract\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use NbsPhp\Core\Mail\BaseMail;

class SendEmailESignAdInsRegisterJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected object $recipient;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($recipient)
    {
        $this->recipient = $recipient;
    }

    public function handle()
    {
        $email = (new BaseMail())
            ->subject('Penerbitan Surat Elektronik')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->line(__("Halo {$this->recipient->name}."))
            ->line(__('Sertifikat Elektronik Anda telah diterbitkan olen PT Indonesia Digital Identity (VIDA) sebagai mitra dari SANFIND. VIDA adalah Penyelengara Sertifikat Elektronik (PSrE) yang diakui oleh Kominfo.<br/><br/>'))
            ->line(__('Anda dapat mengakses Informasi Surat Elektronik Anda di https://sign.vida.id. Terima kasih'));

        return Mail::to($this->recipient->email)->send($email);
    }
}
