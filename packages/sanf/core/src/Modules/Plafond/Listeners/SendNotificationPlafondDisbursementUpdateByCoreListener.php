<?php

namespace Sanf\Core\Modules\Plafond\Listeners;

use Sanf\Core\Modules\Plafond\Jobs\SendNotificationPlafondDisbursementUpdateByCoreForClientJob;
use Sanf\Core\Modules\Plafond\Jobs\SendNotificationPlafondDisbursementUpdateByCoreForCustomerJob;

class SendNotificationPlafondDisbursementUpdateByCoreListener
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

        $clientNotificationDto = (object) [
            'userId' => $content->userId ?? null,
            'statusId' => $content->statusId,
            'client' => $content->client,
            'bowheer' => $content->bowheer,
            'plafondId' => $content->plafondId,
            'disbursementNo' => $content->disbursementNo,
            'disbursementXid' => $content->disbursementXid,
            'submissionXid' => $content->submissionXid,
            'clientId' => $content->clientId,
        ];

        dispatch(new SendNotificationPlafondDisbursementUpdateByCoreForClientJob($clientNotificationDto));

        $customerNotificationDto = (object) [
            'clientId' => $content->clientId,
            'client' => $content->client,
            'bowheerId' => $content->bowheerId,
            'bowheer' => $content->bowheer,
            'disbursementXid' => $content->disbursementXid,
            'submissionXid' => $content->submissionXid,
            'statusId' => $content->statusId,
        ];
        dispatch(new SendNotificationPlafondDisbursementUpdateByCoreForCustomerJob($customerNotificationDto));
    }
}
