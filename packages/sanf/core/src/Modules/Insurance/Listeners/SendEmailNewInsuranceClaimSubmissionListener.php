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

        $adminRecipients = $adminCCMails = [];
        $isDefaultCity = false;
        $sanfAdminEmails = explode(',', $insuranceClaimSubmission->financingUnit->emailCc ?? config('sanf-mobile.mail_to.service'));
        // If selected claim city is equal to the unit city
        if (isset($insuranceClaimSubmission->financingUnit->cityId) && $insuranceClaimSubmission->city_id == $insuranceClaimSubmission->financingUnit->cityId) {
            $isDefaultCity = true;
            // Send to insurance provider
            $adminRecipients = explode(',', $insuranceClaimSubmission->financingUnit->emailProvider);
            // CC to admin sanf
            $adminCCMails = $sanfAdminEmails;
        } else {
            // Send to email cc (admin sanf)
            $adminRecipients = $sanfAdminEmails;
        }
        $userRecipient = (object) [
            'email' => $insuranceClaimSubmission->user->username,
            'fullName' => $insuranceClaimSubmission->user->full_name,
        ];
        dispatch(new SendEmailInsuranceClaimSubmissionForUserJob($insuranceClaimSubmission, $userRecipient));
        dispatch(new SendEmailInsuranceClaimSubmissionForAdminJob($insuranceClaimSubmission, $adminRecipients, $adminCCMails, $isDefaultCity));
    }
}
