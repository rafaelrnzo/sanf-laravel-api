<?php

namespace Sanf\Core\Modules\Prepayment\Listeners;


use Sanf\Core\Modules\Prepayment\Jobs\SendEmailPrepaymentSubmissionForAdminJob;
use Sanf\Core\Modules\Prepayment\Jobs\SendEmailPrepaymentSubmissionForUserJob;

class SendEmailNewPrepaymentSubmissionListener
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
        $prepaymentSubmission = $event->prepaymentSubmission;
        setlocale(LC_ALL, 'id_ID.UTF-8', 'id_ID.UTF-8'); // set locale to use local time Indonesia

        $adminRecipients = explode(',', config('sanf-mobile.mail_to_admin'));
        $userRecipient =(object)[
            'email' =>  $prepaymentSubmission->user->username,
            'fullName' =>  $prepaymentSubmission->user->full_name
        ];
        dispatch(new SendEmailPrepaymentSubmissionForUserJob($prepaymentSubmission, $userRecipient));
        dispatch(new SendEmailPrepaymentSubmissionForAdminJob($prepaymentSubmission, $adminRecipients));
    }
}
