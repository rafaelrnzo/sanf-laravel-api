<?php

namespace NbsPhp\Core\Traits;


use NbsPhp\Core\Notifications\ResetPasswordNotification;

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
        $this->notify(new ResetPasswordNotification($token));
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
        return $this->name;
    }
}
