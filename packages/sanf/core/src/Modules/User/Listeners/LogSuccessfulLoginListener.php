<?php

namespace Sanf\Core\Modules\User\Listeners;


use Carbon\Carbon;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLoginListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object  \$event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;
        $user->last_login_at = Carbon::now();
        $user->save();
    }
}
