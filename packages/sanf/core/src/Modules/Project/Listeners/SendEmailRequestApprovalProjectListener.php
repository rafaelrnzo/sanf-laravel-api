<?php


namespace Sanf\Core\Modules\Project\Listeners;

use Sanf\Core\Modules\Project\Events\AbstractProjectEvent;
use Sanf\Core\Modules\Project\Events\ProjectUpdatedEvent;
use Sanf\Core\Modules\Project\ProjectStatus;
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
        $project = $event->project;
        if ($event instanceof ProjectUpdatedEvent && $project->status_id !== ProjectStatus::WAITING_APPROVAL) {
            return;
        }
        dispatch(new SendEmailProjectApprovalJob($event->project, $recipients));
    }
}
