<?php

namespace NbsPhp\Core\Traits;

use NbsPhp\Core\Enum\UserStatus;
use NbsPhp\Core\Notifications\UserActivationNotification;

trait NeedSetupPassword
{
    /**
     * Determine if the user need setup password.
     *
     * @return bool
     */
    public function needActivation()
    {
        return $this->status_id === UserStatus::NEED_ACTIVATION;
    }

    /**
     * Mark the given user's email as verouteNotificationForMailrified.
     *
     * @return bool
     */
    public function markUserActivated()
    {
        return $this->forceFill([
            'status_id' => UserStatus::ACTIVE,
        ])->save();
    }

    /**
     * Send the email activation notification.
     *
     * @return void
     */
    public function sendUserActivationNotification()
    {
        $notificationClass = config('auth.notifications.user-activation', UserActivationNotification::class);
        $this->notify(new $notificationClass);
    }

    /**
     * Get the email address that should be used for activation.
     *
     * @return string
     */
    public function getEmailForActivation()
    {
        return $this->username;
    }

    /**
     * Get the name that should be used for activation.
     *
     * @return string|null
     */
    public function getNameForActivation()
    {
        return $this->name;
    }
}
