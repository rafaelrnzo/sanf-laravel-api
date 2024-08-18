<?php

namespace Sanf\Core\Modules\Contract\Listeners;

use Sanf\Core\Modules\Contract\Jobs\SendNotificationESignAdInsRegisterJob;

class SendNotificationESignAdInsRegisterListener
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        dispatch(new SendNotificationESignAdInsRegisterJob($event->userId));
    }
}
