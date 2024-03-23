<?php

namespace NbsPhp\Core\Notifications;

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

        return (new BaseMail)
            ->subject('Reset Password')
            ->logo(asset('images/logo-email.png'))
            ->greeting(__('Halo :name,', ['name' => $notifiable->getNameForPasswordReset()]))
            ->line(__('Anda menerima email ini karena kami menerima permintaan pengaturan ulang password untuk akun Anda. Jika Anda tidak meminta pengaturan ulang password, tidak ada tindakan lebih lanjut yang diperlukan.'))
            ->action(__('Reset Password'), $resetUrl)
//            ->line(__('Jika Anda mengalami kesulitan mengklik tombol "Reset Password". Salin dan tempel URL di bawah ini ke web browser Anda:'))
//            ->line("<a href=\"{$resetUrl}\">{$resetUrl}</a>")
            ->to($notifiable->getEmailForPasswordReset(), $notifiable->getNameForPasswordReset());
    }

    protected function resetUrl($notifiable)
    {
        $agent = new Agent();
        $jwtToken = (new \NbsPhp\Core\Jwt\JWTHelper())->newResetPasswordToken($notifiable->getEmailForPasswordReset(), $this->token);
        $resetPasswordUrl = ($agent->isiPhone() || $agent->isiOS() || $agent->isiPad()) ? config('auth.reset_password_ios_url') : config('auth.reset_password_url');
        if ($resetPasswordUrl != '' || $resetPasswordUrl != null) {
            return "{$resetPasswordUrl}?token={$jwtToken}";
        }

        return route('password.reset', ['token' => $jwtToken]);
    }
}
