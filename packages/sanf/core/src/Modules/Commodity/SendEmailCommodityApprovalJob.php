<?php

namespace Sanf\Core\Modules\Commodity;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use NbsPhp\Core\Mail\BaseMail;

class SendEmailCommodityApprovalJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $commodity;

    protected $emailRecipients;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($commodity, $emailRecipients)
    {
        $this->commodity = $commodity;
        $this->emailRecipients = $emailRecipients;
    }

    public function handle()
    {
        $commodityApprovalMail = (new BaseMail())
            ->subject('Pengajuan commodity baru dari pengguna SANFind!')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(file_get_url(optional($this->commodity->image_file)->path) ?? asset('assets/png/email-verification.png'))
            ->line($this->commodity->title)
            ->line($this->commodity->description)
            ->actionApproval([
                [__('Approve Commodity'), route('commodities.approve', ['xid' => $this->commodity->xid])],
                [__('Reject Commodity'), route('commodities.reject', ['xid' => $this->commodity->xid])]
            ]);

        return Mail::to($this->emailRecipients)->send($commodityApprovalMail);
    }
}
