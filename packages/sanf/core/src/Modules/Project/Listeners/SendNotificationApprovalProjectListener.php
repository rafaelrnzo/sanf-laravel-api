<?php

namespace Sanf\Core\Modules\Project\Listeners;

use Sanf\Core\Modules\Project\Jobs\SendNotificationApprovalProjectJob;

class SendNotificationApprovalProjectListener
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        dispatch(new SendNotificationApprovalProjectJob($event->project, $event->user));
    }
}
