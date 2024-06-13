<?php

namespace Sanf\Core\Modules\Plafond\Listeners;

use Sanf\Core\Modules\Plafond\Jobs\SendNotificationPlafondDisbursementSubmittedForClientJob;
use Sanf\Core\Modules\Plafond\Jobs\SendNotificationPlafondDisbursementSubmittedForCustomerJob;

class SendNotificationPlafondDisbursementSubmittedListener
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {

        $content = $event->content;

        dispatch(new SendNotificationPlafondDisbursementSubmittedForClientJob($content->userId));

        $customerNotificationDto = (object) [
            'clientId' => $content->clientId,
            'client' => $content->client,
            'bowheerId' => $content->bowheerId,
            'bowheer' => $content->bowheer,
            'disbursementXid' => $content->disbursementXid,
        ];
        dispatch(new SendNotificationPlafondDisbursementSubmittedForCustomerJob($customerNotificationDto));
    }
}
