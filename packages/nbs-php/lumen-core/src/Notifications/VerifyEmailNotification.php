<?php

namespace NbsPhp\Core\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Jenssegers\Agent\Agent;
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
        $agent = new Agent();
        $emailVerifyUrl = ($agent->isiPhone() || $agent->isiOS() || $agent->isiPad()) ? config(
            'auth.urls.email_verify_ios'
        ) : config('auth.urls.email_verify');

        $id = $notifiable->getKey();
        $token = hash('sha256', $notifiable->getEmailForVerification());
        if ($emailVerifyUrl !== '' || $emailVerifyUrl !== null) {
            return "{$emailVerifyUrl}?id={$id}&token={$token}";
        }

        return route('email.verify', [
            'id' => $id,
            'token' => $token,
        ]);
    }
}
