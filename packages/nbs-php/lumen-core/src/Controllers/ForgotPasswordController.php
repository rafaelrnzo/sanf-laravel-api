<?php

namespace NbsPhp\Core\Controllers;

use NbsPhp\Core\Traits\SendsPasswordResetEmails;

class ForgotPasswordController extends RestApiController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;
}
