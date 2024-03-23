<?php

namespace Sanf\Core\Modules\Project\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use NbsPhp\Core\Mail\BaseMail;

class SendEmailProjectApprovalJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $project;

    protected $emailRecipients;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($project, $emailRecipients)
    {
        $this->project = $project;
        $this->emailRecipients = $emailRecipients;
    }

    public function handle()
    {
        $projectApprovalMail = (new BaseMail())
            ->subject('Pengajuan project baru dari pengguna SANFIND!')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(
                file_get_url(optional($this->project->image_file)->path) ?? asset('assets/png/email-verification.png')
            )
            ->line($this->project->title)
            ->line($this->project->description)
            ->actionApproval([
                [__('Approve Project'), route('projects.approve', ['xid' => $this->project->xid])],
                [__('Reject Project'), route('projects.reject', ['xid' => $this->project->xid])],
            ]);

        $ccMails = explode(',', config('sanf-mobile.mail_to.it_helpdesk'));

        return Mail::to($this->emailRecipients)
            ->cc($ccMails)
            ->send($projectApprovalMail);
    }
}
