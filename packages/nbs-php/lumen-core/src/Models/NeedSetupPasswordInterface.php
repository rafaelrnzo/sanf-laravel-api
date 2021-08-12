<?php

namespace NbsPhp\Core\Models;

interface NeedSetupPasswordInterface
{
    /**
     * Determine if the user need setup password.
     *
     * @return bool
     */
    public function needActivation();

    /**
     * Mark the given user's email as verouteNotificationForMailrified.
     *
     * @return bool
     */
    public function markUserActivated();

    /**
     * Send the email activation notification.
     *
     * @return void
     */
    public function sendUserActivationNotification();

    /**
     * Get the email address that should be used for activation.
     *
     * @return string
     */
    public function getEmailForActivation();

    /**
     * Get the name that should be used for activation.
     *
     * @return string|null
     */
    public function getNameForActivation();
}
