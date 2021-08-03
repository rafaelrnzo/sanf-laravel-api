<?php

namespace NbsPhp\Core\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Jenssegers\Agent\Agent;
use NbsPhp\Core\Mail\BaseMail;

class AccountActivationNotification extends Notification
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
        // TODO: Get verification URL
        $verification = '';
        $reportUrl = '';

        return (new BaseMail)
            ->subject('Aktivasi akun SANF Anda!')
            ->leftLogo(asset('assets/svg/sanf-logo-blue.svg'))
            ->rightLogo(asset('assets/svg/sanf-tagline.svg'))
            ->banner(asset('assets/svg/email-verification.svg'))
            ->greeting(__('Halo :name!', ['name' => $notifiable->getNameForPasswordReset()]))
            ->line(__('Mohon verifikasi email Anda dengan mengklik tombol di bawah ini'))
            ->action(__('Verifikasi Email'), $verification)
            ->lineWithUrl(
                __('Kami menerima permintaan pembuatan akun SANFXtra yang memakai email Anda. Jika Anda merasa tidak membuat request tersebut mohon abaikan email ini atau anda dapat'),
                [__('laporkan email ini'), $reportUrl]
            )
            ->to($notifiable->getEmailForPasswordReset(), $notifiable->getNameForPasswordReset());
    }


    protected function resetUrl($notifiable)
    {
        $agent = new Agent();
        $jwtToken = (new \NbsPhp\Core\JWTHelper())->newResetPasswordToken($notifiable->getEmailForPasswordReset(), $this->token);
        $resetPasswordUrl = ($agent->isiPhone() || $agent->isiOS() || $agent->isiPad()) ? config('auth.reset_password_ios_url') : config('auth.reset_password_url') ;
        if ($resetPasswordUrl != '' || $resetPasswordUrl != null) {
            return "{$resetPasswordUrl}?token={$jwtToken}";
        }
        return route('password.reset', ['token' => $jwtToken]);
    }
}
