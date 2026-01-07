<?php

namespace Sanf\Core\Modules\Payment\Listeners;

use Sanf\Core\Modules\Payment\Jobs\SendPushNotificationPaymentExpiredJob;

class SendNotificationPaymentExpiredListener
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        dispatch(new SendPushNotificationPaymentExpiredJob($event->payment));
    }
}
