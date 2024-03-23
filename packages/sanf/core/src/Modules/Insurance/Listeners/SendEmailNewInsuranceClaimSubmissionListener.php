<?php

namespace Sanf\Core\Modules\Insurance\Listeners;

use Sanf\Core\Modules\Insurance\Jobs\SendEmailInsuranceClaimSubmissionForAdminJob;
use Sanf\Core\Modules\Insurance\Jobs\SendEmailInsuranceClaimSubmissionForUserJob;

class SendEmailNewInsuranceClaimSubmissionListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        $insuranceClaimSubmission = $event->insuranceClaimSubmission;
        $adminRecipients = explode(',', config('sanf-mobile.mail_to.service'));
        $userRecipient = (object) [
            'email' => $insuranceClaimSubmission->user->username,
            'fullName' => $insuranceClaimSubmission->user->full_name,
        ];
        dispatch(new SendEmailInsuranceClaimSubmissionForUserJob($insuranceClaimSubmission, $userRecipient));
        dispatch(new SendEmailInsuranceClaimSubmissionForAdminJob($insuranceClaimSubmission, $adminRecipients));
    }
}
