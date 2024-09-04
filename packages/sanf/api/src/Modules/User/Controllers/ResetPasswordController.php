<?php

namespace Sanf\Api\Modules\User\Controllers;

use Illuminate\Auth\Passwords\PasswordBroker;
use NbsPhp\Core\Controllers\ResetPasswordController as ParentController;
use Sanf\Core\Passwords\SodiumPassword;

class ResetPasswordController extends ParentController
{
    /**
     * Get the broker to be used during password reset.
     *
     * @return PasswordBroker
     */
    public function broker()
    {
        return SodiumPassword::broker();
    }
}
