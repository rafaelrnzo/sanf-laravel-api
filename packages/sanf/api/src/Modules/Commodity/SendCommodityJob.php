<?php

namespace Sanf\Api\Modules\Commodity;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use NbsPhp\Core\Mail\BaseMail;

class SendCommodityJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $emailSender;

    protected $emailRecipients;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($emailSender, $emailRecipients)
    {
        $this->emailSender = $emailSender;
        $this->emailRecipients = $emailRecipients;
    }

    public function handle()
    {

        $commodityApprovalMail = (new BaseMail())
            ->subject('Pengajuan komoditi baru dari pengguna SANFXtra!')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->line(__('Lorem ipsum dolor sit amet consectetur adipisicing elit. Nesciunt obcaecati dolores quod,
                perferendis debitis ad dolor architecto repellat nulla, sunt, error nobis laborum ratione.
                Magnam explicabo dicta repellendus cupiditate unde?
            '))
            ->actionApproval([
                [__('Approve Komoditi'), '#'], //TODO: implement url to approve
                [__('Reject Komoditi'), '#'] //TODO: implement url to reject
            ]);

        $commodityApprovalMail->from($this->emailSender[0], $this->emailSender[1]);

        return Mail::to($this->emailRecipients)->send($commodityApprovalMail);
    }
}
