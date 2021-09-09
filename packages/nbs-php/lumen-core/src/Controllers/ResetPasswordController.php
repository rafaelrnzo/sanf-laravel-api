<?php

namespace NbsPhp\Core\Controllers;

use NbsPhp\Core\Traits\ResetsPasswords;

class ResetPasswordController extends RestApiController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */
    use ResetsPasswords;
}
