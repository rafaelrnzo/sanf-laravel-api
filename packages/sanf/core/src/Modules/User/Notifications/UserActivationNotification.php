<?php

namespace Sanf\Core\Modules\User\Notifications;

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
        // TODO: Report Url
        $reportUrl = '';

        return (new BaseMail)
            ->subject('Aktivasi akun SANF Anda!')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->greeting(__('Halo :name!', ['name' => $fullName]))
            ->line(__('Mohon verifikasi email Anda dengan mengklik tombol di bawah ini'))
            ->action(__('Verifikasi Email'), $verificationUrl)
            ->lineWithUrl(
                __('Kami menerima permintaan pembuatan akun SANFXtra yang memakai email Anda. Jika Anda merasa tidak membuat request tersebut mohon abaikan email ini atau anda dapat'),
                [__('laporkan email ini'), $reportUrl]
            )
            ->to($notifiable->getEmailForPasswordReset(), $fullName);
    }

    protected function activationUrl($notifiable)
    {
        $agent = new Agent();
        $userActivationUrl = ($agent->isiPhone() || $agent->isiOS() || $agent->isiPad()) ? config('auth.urls.user_activation_ios') : config('auth.urls.user_activation');
        $email = $notifiable->getEmailForVerification();
        //TODO CONFIGURABLE TOKEN DURATION
        $tokenDuration = 60 * 60; //1 hours
        $token = sha1($email);
        $jwtToken = (new \NbsPhp\Core\Jwt\JWTHelper())->newVerifyEmailToken($notifiable->getKey(), $token, $tokenDuration);
        if ($userActivationUrl !== '' || $userActivationUrl !== null) {
            return "{$userActivationUrl}?email={$email}&token={$jwtToken}";
        }

        return route('user.activate', [
            'email' => $email,
            'token' => $jwtToken,
        ]);
    }
}
