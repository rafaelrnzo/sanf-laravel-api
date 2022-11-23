<?php

namespace Sanf\Core\Modules\Notification\Listeners;

use Sanf\Core\Modules\Notification\Jobs\SendPushNotificationByExternalJob;

class SendPushNotificationByExternalListener
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        foreach ($event->contents as $content) {
            dispatch(new SendPushNotificationByExternalJob($content));
        }
    }
}
