<?php

namespace Sanf\Core\Modules\Contract\Listeners;

use Sanf\Core\Modules\Contract\Jobs\SendNotificationESignDocumentSignCompleteJob;

class SendNotificationESignDocumentSignCompleteListener
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        dispatch(new SendNotificationESignDocumentSignCompleteJob($event->userId, $event->documentName));
    }
}
