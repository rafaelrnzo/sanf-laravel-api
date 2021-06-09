<?php

namespace NbsPhp\Core\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use NbsPhp\Core\Mail\BaseMail;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    public function via()
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $fullName = $notifiable->getNameForVerification();
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new BaseMail())
            ->subject('Email Verification')
            ->logo(asset('images/logo-email.png'))
            ->greeting(__('Terima Kasih Telah Bergabung Dengan Kami,'))
            ->action(__('Verifikasi'), $verificationUrl)
            //->line(__('Jika Anda mengalami kesulitan mengklik tombol "Verifikasi". Salin dan tempel URL di bawah ini ke web browser Anda:'))
            //->line("<a href=\"{$verificationUrl}\">{$verificationUrl}</a>")
            ->to($notifiable->getEmailForVerification(), $fullName);
    }

    protected function verificationUrl($notifiable)
    {
        $emailVerifyUrl = config('auth.email_verify_url');

        if ($emailVerifyUrl != '' || $emailVerifyUrl != null) {
            return URL::to($emailVerifyUrl, [
                'id' => $notifiable->getKey(),
                'token' => sha1($notifiable->getEmailForVerification()),
            ]);
        }

        return route('email.verify', [
            'id' => $notifiable->getKey(),
            'token' => sha1($notifiable->getEmailForVerification()),
        ]);
    }
}
