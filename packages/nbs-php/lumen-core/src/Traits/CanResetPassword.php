<?php

namespace NbsPhp\Core\Traits;


use NbsPhp\Core\Notifications\ResetPasswordNotification;
use NbsPhp\Core\Notifications\VerifyEmailNotification;

trait CanResetPassword
{
    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $notificationClass = config('auth.notifications.reset-password', ResetPasswordNotification::class);
        $this->notify(new $notificationClass($token));
    }

    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->username;
    }

    /**
     * Get the name where password reset links are sent.
     *
     * @return string|null
     */
    public function getNameForPasswordReset()
    {
        return $this->full_name;
    }
}
