<?php

namespace Sanf\Core\Modules\Payment\Listeners;

use Sanf\Core\Modules\Payment\Jobs\SendPushNotificationPaymentCreatedJob;

class SendNotificationPaymentCreatedListener
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        dispatch(new SendPushNotificationPaymentCreatedJob($event->payment));
    }
}
