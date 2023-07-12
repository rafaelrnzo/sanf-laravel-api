<?php

namespace NbsPhp\Core\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Jenssegers\Agent\Agent;
use NbsPhp\Core\Mail\BaseMail;

class UserActivationNotification extends Notification
{
    use Queueable;

    public function via()
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $fullName = $notifiable->getNameForVerification();
        $verificationUrl = $this->activationUrl($notifiable);

        return (new BaseMail())
            ->subject('Email Verification')
            ->logo(asset('images/logo-email.png'))
            ->greeting(__('Terima Kasih Telah Bergabung Dengan Kami,'))
            ->action(__('Verifikasi'), $verificationUrl)
            //->line(__('Jika Anda mengalami kesulitan mengklik tombol "Verifikasi". Salin dan tempel URL di bawah ini ke web browser Anda:'))
            //->line("<a href=\"{$verificationUrl}\">{$verificationUrl}</a>")
            ->to($notifiable->getEmailForVerification(), $fullName);
    }

    protected function activationUrl($notifiable)
    {
        $agent = new Agent();
        $userActivationUrl = ($agent->isiPhone() || $agent->isiOS() || $agent->isiPad()) ? config(
            'auth.urls.user_activation_ios'
        ) : config('auth.urls.user_activation');

        $id = $notifiable->getKey();
        $token = hash('sha256', $notifiable->getEmailForVerification());
        if ($userActivationUrl !== '' || $userActivationUrl !== null) {
            return "{$userActivationUrl}?id={$id}&token={$token}";
        }

        return route('email.verify', [
            'id' => $id,
            'token' => $token,
        ]);
    }
}
