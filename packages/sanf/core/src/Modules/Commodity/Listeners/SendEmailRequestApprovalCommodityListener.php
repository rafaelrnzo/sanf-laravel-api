<?php


namespace Sanf\Core\Modules\Commodity\Listeners;


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
        dispatch(new SendEmailCommodityApprovalJob($event->commodity, $recipients));
    }
}
