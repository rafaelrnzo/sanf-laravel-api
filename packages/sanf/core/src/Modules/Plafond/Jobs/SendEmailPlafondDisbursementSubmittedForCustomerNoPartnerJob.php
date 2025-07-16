<?php

namespace Sanf\Core\Modules\Plafond\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Sanf\Core\Mail\PlafondDisbursementSubmittedMailable;

class SendEmailPlafondDisbursementSubmittedForCustomerNoPartnerJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $data;
    protected $recipients;
    protected $ccMails;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $recipients, $ccMails)
    {
        $this->data = $data;
        $this->recipients = $recipients;
        $this->ccMails = $ccMails;
    }

    public function handle()
    {
        $appUrl = config('app.url');

        $mailable = new PlafondDisbursementSubmittedMailable($this->data, $appUrl);

        return Mail::to($this->recipients)
            ->cc($this->ccMails)
            ->send($mailable);
    }
}
