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

        return (new BaseMail)
            ->subject('Reset Password')
            ->logo(asset('assets/svg/sanf-logo.svg'))
            ->greeting(__('Reset Password Akun SANF'))
            ->line(__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed sollicitudin arcu felis, a suscipit arcu fringilla at. Nunc ante dolor, gravida quis ante vel, eleifend porta nunc.'))
            ->action(__('Reset Password'), $resetUrl)
            ->line(__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed sollicitudin arcu felis, a suscipit arcu fringilla at. Nunc ante dolor, gravida quis ante vel, eleifend porta nunc.'))
//            ->line(__('Jika Anda mengalami kesulitan mengklik tombol "Reset Password". Salin dan tempel URL di bawah ini ke web browser Anda:'))
//            ->line("<a href=\"{$resetUrl}\">{$resetUrl}</a>")
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
