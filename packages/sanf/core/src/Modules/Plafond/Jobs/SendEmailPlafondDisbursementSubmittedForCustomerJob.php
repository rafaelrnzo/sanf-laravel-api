<?php

namespace Sanf\Core\Modules\Plafond\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Sanf\Core\Mail\PlafondDisbursementSubmittedMailable;

class SendEmailPlafondDisbursementSubmittedForCustomerJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $data;
    protected $recipients;
    protected $customerReview;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $recipients, $customerReview = false)
    {
        $this->data = $data;
        $this->recipients = $recipients;
        $this->customerReview = $customerReview;
    }

    public function handle()
    {
        $appUrl = config('app.url');

        $marketingMail = explode(',', config('sanf-mobile.mail_to.marketing'));

        $mailable = new PlafondDisbursementSubmittedMailable($this->data, $appUrl);

        if ($this->customerReview) {
            return Mail::to($this->recipients)
                ->cc($marketingMail)
                ->send($mailable);
        } else {
            return Mail::to($marketingMail)
                ->send($mailable);
        }
    }
}
