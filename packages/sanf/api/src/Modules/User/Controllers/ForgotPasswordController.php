<?php

namespace Sanf\Api\Modules\User\Controllers;

use NbsPhp\Core\Controllers\ForgotPasswordController as ParentController;
use Sanf\Core\Passwords\SodiumPassword;

class ForgotPasswordController extends ParentController
{
    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return SodiumPassword::broker();
    }
}
