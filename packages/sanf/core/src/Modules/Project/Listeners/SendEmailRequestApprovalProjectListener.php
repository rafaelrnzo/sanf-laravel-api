<?php


namespace Sanf\Core\Modules\Project\Listeners;

use Sanf\Core\Modules\Project\Events\AbstractProjectEvent;
use Sanf\Core\Modules\Project\SendEmailProjectApprovalJob;

class SendEmailRequestApprovalProjectListener
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle(AbstractProjectEvent $event)
    {
        $recipients = explode(',', config('sanf-mobile.mail_to_admin'));
        dispatch(new SendEmailProjectApprovalJob($event->project, $recipients));
    }
}
