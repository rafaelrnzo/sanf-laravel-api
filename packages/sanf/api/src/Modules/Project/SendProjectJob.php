<?php

namespace Sanf\Api\Modules\Project;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use NbsPhp\Core\Mail\BaseMail;

class SendProjectJob implements ShouldQueue
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

        $projectApprovalMail = (new BaseMail())
            ->subject('Pengajuan project baru dari pengguna SANFXtra!')
            ->leftLogo(asset('assets/svg/sanf-logo-blue.svg'))
            ->rightLogo(asset('assets/svg/sanf-tagline.svg'))
            ->banner(asset('assets/svg/email-verification.svg'))
            ->line(__('Lorem ipsum dolor sit amet consectetur adipisicing elit. Nesciunt obcaecati dolores quod,
                perferendis debitis ad dolor architecto repellat nulla, sunt, error nobis laborum ratione.
                Magnam explicabo dicta repellendus cupiditate unde?
            '))
            ->actionApproval([
                [__('Approve Project'), '#'], //TODO: implement url to approve
                [__('Reject Project'), '#'] //TODO: implement url to reject
            ]);

        $projectApprovalMail->from($this->emailSender[0], $this->emailSender[1]);

        return Mail::to($this->emailRecipients)->send($projectApprovalMail);
    }
}
