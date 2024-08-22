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

        if ($content->customerReview) {
            $customerNotificationDto = (object) [
                'clientId' => $content->clientId,
                'client' => $content->client,
                'bowheerId' => $content->bowheerId,
                'bowheer' => $content->bowheer,
                'disbursementXid' => $content->disbursementXid,
                'submissionXid' => $content->submissionXid,
                'totalAmount' => 'Rp. ' . number_format($content->totalAmount, 0, ',', '.'),
            ];
            dispatch(new SendNotificationPlafondDisbursementSubmittedForCustomerJob($customerNotificationDto));
        }
    }
}
