<?php

namespace Sanf\Core\Modules\Commodity\Listeners;

use Sanf\Core\Modules\Commodity\Jobs\SendNotificationRejectCommodityJob;

class SendNotificationRejectCommodityListener
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        dispatch(new SendNotificationRejectCommodityJob($event->commodity, $event->user));
    }
}
