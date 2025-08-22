<?php

namespace Sanf\Core\Modules\PdcHold\Listeners;

use Sanf\Core\Modules\PdcHold\Jobs\SendNotificationPdcHoldSubmissionUpdateByCoreForClientJob;

/**
 * @since CR2025
 */
class SendNotificationPdcHoldSubmissionUpdateByCoreListener
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        dispatch(new SendNotificationPdcHoldSubmissionUpdateByCoreForClientJob($event->content));
    }
}
