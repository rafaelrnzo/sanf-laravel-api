<?php

namespace Sanf\Api\Modules\ContactUs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Sanf\Core\Mail\BaseMailV2;

class SendAskUsJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $email;
    protected $emailRecipients;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $emailRecipients)
    {
        $this->email = $email;
        $this->emailRecipients = $emailRecipients;
    }

    public function handle()
    {
        $askUsEmail = (new BaseMailV2())
            ->subject('Kritik dan saran dari pengguna SANFIND!')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->writeInto($this->email);

        if (is_array($this->email['images'])) {
            foreach ($this->email['images'] as $val) {
                $askUsEmail->attach(public_path($val['path']));
            }
        }

        $askUsEmail->from($this->email['email'], $this->email['name']);

        $ccMails = explode(',', config('sanf-mobile.mail_to.it_helpdesk'));
        Mail::to($this->emailRecipients)
            ->cc($ccMails)
            ->send($askUsEmail);
    }
}
