<?php

namespace Sanf\Core\Modules\User\Notifications;

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
        $adminMail = config('sanf-mobile.mail_to_admin');
        $reportUrl = "mailto:{$adminMail}?subject=Laporan Aktivasi Akun";

        return (new BaseMail())
            ->subject('Registrasi berhasil! Silakan aktivasi akun Anda')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->line(__('Mohon verifikasi email Anda dengan mengklik tombol di bawah ini'))
            ->action(__('Verifikasi Email'), $verificationUrl)
            ->lineWithUrl(
                __(
                    'Kami menerima permintaan pembuatan akun SANFIND yang memakai email Anda. Jika Anda merasa tidak membuat request tersebut mohon abaikan email ini atau anda dapat'
                ),
                [__('laporkan email ini'), $reportUrl]
            )
            ->to($notifiable->getEmailForVerification(), $fullName);
    }

    protected function verificationUrl($notifiable)
    {
        $agent = new Agent();
        $emailVerifyUrl = ($agent->isiPhone() || $agent->isiOS() || $agent->isiPad()) ? config(
            'auth.urls.email_verify_ios'
        ) : config('auth.urls.email_verify');

        //TODO CONFIGURABLE TOKEN DURATION
        $tokenDuration = 60 * 60; //1 hours
        $token = hash('sha256', $notifiable->getEmailForVerification());
        $jwtToken = (new \NbsPhp\Core\Jwt\JWTHelper())->newVerifyEmailToken(
            $notifiable->getKey(),
            $token,
            $tokenDuration
        );
        if ($emailVerifyUrl !== '' || $emailVerifyUrl !== null) {
            return "{$emailVerifyUrl}?token={$jwtToken}";
        }

        return route('email.verify', [
            'token' => $jwtToken,
        ]);
    }
}
