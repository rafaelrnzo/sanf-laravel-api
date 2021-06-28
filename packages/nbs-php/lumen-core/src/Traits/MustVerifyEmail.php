<?php

namespace NbsPhp\Core\Traits;

use Carbon\Carbon;
use NbsPhp\Core\Notifications\VerifyEmailNotification;

trait MustVerifyEmail
{
    /**
     * Determine if the user has verified their email address.
     *
     * @return bool
     */
    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Mark the given user's email as verouteNotificationForMailrified.
     *
     * @return bool
     */
    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => Carbon::now(),
        ])->save();
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $notificationClass = config('auth.notifications.verify-email', VerifyEmailNotification::class);
        $this->notify(new $notificationClass);
    }

    /**
     * Get the email address that should be used for verification.
     *
     * @return string
     */
    public function getEmailForVerification()
    {
        return $this->username;
    }

    /**
     * Get the email address that should be used for verification.
     *
     * @return string|null
     */
    public function getNameForVerification()
    {
        return $this->name;
    }
}
