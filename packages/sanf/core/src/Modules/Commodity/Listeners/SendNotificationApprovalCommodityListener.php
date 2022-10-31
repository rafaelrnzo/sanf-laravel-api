<?php


namespace Sanf\Core\Modules\Commodity\Listeners;


use Sanf\Core\Modules\Commodity\Jobs\SendNotificationApprovalCommodityJob;

class SendNotificationApprovalCommodityListener
{

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        dispatch(new SendNotificationApprovalCommodityJob($event->commodity, $event->user));
    }
}
