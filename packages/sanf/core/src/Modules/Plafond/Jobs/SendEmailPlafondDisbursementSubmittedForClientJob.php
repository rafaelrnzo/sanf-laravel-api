<?php

namespace Sanf\Core\Modules\Plafond\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Sanf\Core\Mail\PlafondDisbursementSubmittedMailable;

class SendEmailPlafondDisbursementSubmittedForClientJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $data;
    protected $recipients;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $recipients)
    {
        $this->data = $data;
        $this->recipients = $recipients;
    }

    public function handle()
    {
        $appUrl = config('app.url');

        if (isset($this->data['to'])) {
            unset($this->data['to']);
        }

        $mailable = new PlafondDisbursementSubmittedMailable($this->data, $appUrl);

        return Mail::to($this->recipients)->send($mailable);
    }
}
