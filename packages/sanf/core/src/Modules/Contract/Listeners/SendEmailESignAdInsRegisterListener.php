<?php

namespace Sanf\Core\Modules\Contract\Listeners;

use Sanf\Core\Modules\Contract\Jobs\SendEmailESignAdInsRegisterJob;

class SendEmailESignAdInsRegisterListener
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        dispatch(new SendEmailESignAdInsRegisterJob($event->recipient));
    }
}
