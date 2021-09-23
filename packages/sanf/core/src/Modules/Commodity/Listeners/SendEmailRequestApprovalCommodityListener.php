<?php


namespace Sanf\Core\Modules\Commodity\Listeners;



use Illuminate\Support\Facades\Log;

class SendEmailRequestApprovalCommodityListener
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        Log::info('JOB SEND EMAIL APPROVAL DISPATCHED');
        //TODO DISPATCH JOB HERE
    }
}
