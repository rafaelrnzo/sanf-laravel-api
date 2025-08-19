<?php

namespace Sanf\Core\Modules\PdcHold\Listeners;

use NbsPhp\Core\Event;
use Sanf\Core\Modules\PdcHold\Jobs\SendEmailPdcHoldMultiContractSubmissionForAdminJob;
use Sanf\Core\Modules\PdcHold\Jobs\SendEmailPdcHoldMultiContractSubmissionForUserJob;

/**
 * @since CR2025
 */
class SendEmailSubmitPdcHoldMultiContractListener extends Event
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        $pdcHoldSubmission = $event->pdcHoldSubmission;
        $adminRecipients = explode(',', config('sanf-mobile.mail_to_admin'));
        $userRecipient = (object) [
            'email' => $event->clientUser->username,
            'fullName' => $event->clientUser->full_name,
        ];
        dispatch(new SendEmailPdcHoldMultiContractSubmissionForUserJob($pdcHoldSubmission, $userRecipient));
        dispatch(new SendEmailPdcHoldMultiContractSubmissionForAdminJob($pdcHoldSubmission, $userRecipient, $adminRecipients));
    }
}
