<?php


namespace Sanf\Core\Modules\Commodity\Listeners;


use Sanf\Core\Modules\Commodity\CommodityStatus;
use Sanf\Core\Modules\Commodity\Events\CommodityUpdatedEvent;
use Sanf\Core\Modules\Commodity\SendEmailCommodityApprovalJob;

class SendEmailRequestApprovalCommodityListener
{

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        $recipients = explode(',', config('sanf-mobile.mail_to_admin'));
        $commodity = $event->commodity;
        if ($event instanceof CommodityUpdatedEvent && $commodity->status_id !== CommodityStatus::WAITING_APPROVAL) {
            return;
        }
        dispatch(new SendEmailCommodityApprovalJob($commodity, $recipients));
    }
}
