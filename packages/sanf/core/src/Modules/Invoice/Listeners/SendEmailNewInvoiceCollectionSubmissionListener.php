<?php

namespace Sanf\Core\Modules\Invoice\Listeners;


use Sanf\Core\Modules\Invoice\Jobs\SendEmailInvoiceCollectionSubmissionForAdminJob;
use Sanf\Core\Modules\Invoice\Jobs\SendEmailInvoiceCollectionSubmissionForUserJob;

class SendEmailNewInvoiceCollectionSubmissionListener
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
        $invoiceCollectionSubmissions = $event->invoiceCollectionSubmissions;
        $adminRecipients = explode(',', config('sanf-mobile.mail_to_admin'));
        $userRecipient =(object)[
            'email' =>  $invoiceCollectionSubmissions[0]->user->username,
            'fullName' =>  $invoiceCollectionSubmissions[0]->user->full_name
        ];
        dispatch(new SendEmailInvoiceCollectionSubmissionForUserJob($invoiceCollectionSubmissions, $userRecipient));
        dispatch(new SendEmailInvoiceCollectionSubmissionForAdminJob($invoiceCollectionSubmissions, $adminRecipients));
    }
}
