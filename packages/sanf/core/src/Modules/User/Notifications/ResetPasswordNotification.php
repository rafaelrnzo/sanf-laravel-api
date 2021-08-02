<?php

namespace Sanf\Core\Modules\User\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
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
            ->leftLogo(asset('assets/svg/sanf-logo-blue.svg'))
            ->rightLogo(asset('assets/svg/sanf-tagline.svg'))
            ->banner(asset('assets/svg/change-password.svg'))
            ->line(__('Seseorang telah mencoba mereset password akun Anda. Jika benar, mohon verifikasi email Anda dengan mengklik tombol di bawah ini.'))
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
        $jwtToken = (new \NbsPhp\Core\JWTHelper())->newResetPasswordToken($notifiable->getEmailForPasswordReset(), $this->token);
        $resetPasswordUrl = ($agent->isiPhone() || $agent->isiOS() || $agent->isiPad()) ? config('auth.urls.reset_password_ios') : config('auth.urls.reset_password') ;
        if ($resetPasswordUrl != '' || $resetPasswordUrl != null) {
            return "{$resetPasswordUrl}?token={$jwtToken}";
        }
        return route('password.reset', ['token' => $jwtToken]);
    }
}
