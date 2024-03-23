<?php

namespace Sanf\Core\Modules\Prepayment\Listeners;

use Sanf\Core\Modules\Prepayment\Jobs\SendEmailPrepaymentSubmissionForAdminJob;
use Sanf\Core\Modules\Prepayment\Jobs\SendEmailPrepaymentSubmissionForUserJob;

class SendEmailNewPrepaymentSubmissionListener
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        $prepaymentSubmission = $event->prepaymentSubmission;
        $adminRecipients = explode(',', config('sanf-mobile.mail_to.marketing'));
        $userRecipient = (object) [
            'email' => $prepaymentSubmission->user->username,
            'fullName' => $prepaymentSubmission->user->full_name,
        ];
        dispatch(new SendEmailPrepaymentSubmissionForUserJob($prepaymentSubmission, $userRecipient));
        dispatch(new SendEmailPrepaymentSubmissionForAdminJob($prepaymentSubmission, $adminRecipients));
    }
}
