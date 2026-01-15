<?php

namespace Sanf\Core\Modules\Disbursement\Listeners;

use Sanf\Core\Modules\Disbursement\Jobs\SendPushNotificationSparePartDisbursementValidatedJob;

class SendNotificationSparePartDisbursementValidatedListener
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        dispatch(new SendPushNotificationSparePartDisbursementValidatedJob($event->bowheerId, $event->batchNumber));
    }
}
