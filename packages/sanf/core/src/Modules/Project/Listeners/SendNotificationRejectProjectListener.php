<?php

namespace Sanf\Core\Modules\Project\Listeners;

use Sanf\Core\Modules\Project\Jobs\SendNotificationRejectProjectJob;

class SendNotificationRejectProjectListener
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        dispatch(new SendNotificationRejectProjectJob($event->project, $event->user));
    }
}
