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
            ->logo(asset('assets/svg/sanf-logo.svg'))
            ->greeting(__('Terima Kasih Telah Bergabung Dengan Kami,'))
            ->action(__('Verifikasi'), $verificationUrl)
            //->line(__('Jika Anda mengalami kesulitan mengklik tombol "Verifikasi". Salin dan tempel URL di bawah ini ke web browser Anda:'))
            //->line("<a href=\"{$verificationUrl}\">{$verificationUrl}</a>")
            ->to($notifiable->getEmailForVerification(), $fullName);
    }

    protected function verificationUrl($notifiable)
    {
        $agent = new Agent();
        $emailVerifyUrl = ($agent->isiPhone() || $agent->isiOS() || $agent->isiPad()) ? config('auth.reset_verify_ios_url') : config('auth.email_verify_url') ;

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
