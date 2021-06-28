<?php

namespace Sanf\Core\Modules\User\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
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
            ->logo(asset('assets/png/sanf-logo.png'))
            ->greeting(__('Aktivasi Akun SANF'))
            ->line(__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed sollicitudin arcu felis, a suscipit arcu fringilla at. Nunc ante dolor, gravida quis ante vel, eleifend porta nunc.'))
            ->action(__('Aktivasi Akun'), $verificationUrl)
            ->line(__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed sollicitudin arcu felis, a suscipit arcu fringilla at. Nunc ante dolor, gravida quis ante vel, eleifend porta nunc.'))
            //->line(__('Jika Anda mengalami kesulitan mengklik tombol "Verifikasi". Salin dan tempel URL di bawah ini ke web browser Anda:'))
            //->line("<a href=\"{$verificationUrl}\">{$verificationUrl}</a>")
            ->to($notifiable->getEmailForVerification(), $fullName);
    }

    protected function verificationUrl($notifiable)
    {
        $agent = new Agent();
        $emailVerifyUrl = ($agent->isiPhone() || $agent->isiOS() || $agent->isiPad()) ? config('auth.urls.reset_verify_ios') : config('auth.urls.email_verify');
        $id = $notifiable->getKey();
        $token = sha1($notifiable->getEmailForVerification());
        if ($emailVerifyUrl !== '' || $emailVerifyUrl !== null) {
            return "{$emailVerifyUrl}/{$id}/{$token}";
        }
        return route('email.verify', ['id' => $id, 'token' => $token]);
    }
}
