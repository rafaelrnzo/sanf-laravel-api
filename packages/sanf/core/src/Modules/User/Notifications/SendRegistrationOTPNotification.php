<?php

namespace Sanf\Core\Modules\User\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NbsPhp\Core\Mail\BaseMail;

class SendRegistrationOTPNotification extends Notification
{
    use Queueable;

    protected $otpCode;

    public function __construct(string $otpCode)
    {
        $this->otpCode = $otpCode;
    }

    public function via()
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $fullName = $notifiable->full_name ?? 'User';

        return (new BaseMail())
            ->subject('Kode OTP Verifikasi Registrasi SANFIND')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->line("Halo {$fullName},")
            ->line('Berikut adalah kode OTP untuk memverifikasi akun Anda:')
            ->line("<h2 style='text-align: center; letter-spacing: 5px;'>{$this->otpCode}</h2>")
            ->line('Kode ini berlaku selama 5 menit. Jangan berikan kode ini kepada siapa pun.')
            ->line('Jika Anda tidak merasa melakukan registrasi di SANFIND, silakan abaikan email ini.')
            ->to($notifiable->username, $fullName);
    }
}
