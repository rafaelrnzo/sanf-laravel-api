<?php

namespace Sanf\Core\Modules\User\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Jenssegers\Agent\Agent;
use NbsPhp\Core\Mail\BaseMail;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via()
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $resetUrl = $this->resetUrl($notifiable);
        $reportUrl = 'https://www.google.com';

        return (new BaseMail)
            ->subject('Reset Password')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/change-password.png'))
            ->line(__(
                'Seseorang telah mencoba mereset password akun Anda.
                <br />
                <blockquote style="margin: 0 3em;font-size: 16px; line-height: 150%;">
                    Jika benar, mohon verifikasi email Anda dengan mengklik tombol di bawah ini.
                </blockquote>
            '))
            ->action(__('Reset Password'), $resetUrl)
            ->lineWithUrl(
                __('Jika Anda merasa tidak membuat request tersebut mohon abaikan email ini atau Anda dapat'),
                [__('laporkan email ini'), $reportUrl]
            )
            ->to($notifiable->getEmailForPasswordReset(), $notifiable->getNameForPasswordReset());
    }


    protected function resetUrl($notifiable)
    {
        $agent = new Agent();
        $tokenDuration = 60 * 60; //1 hours
        $jwtToken = (new \NbsPhp\Core\Jwt\JWTHelper())->newResetPasswordToken($notifiable->getEmailForPasswordReset(), $this->token, $tokenDuration);
        $resetPasswordUrl = ($agent->isiPhone() || $agent->isiOS() || $agent->isiPad()) ? config('auth.urls.reset_password_ios') : config('auth.urls.reset_password');
        if ($resetPasswordUrl != '' || $resetPasswordUrl != null) {
            return "{$resetPasswordUrl}?token={$jwtToken}";
        }
        return route('password.reset', ['token' => $jwtToken]);
    }
}
