<?php

namespace Sanf\Core\Modules\Project;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use NbsPhp\Core\Mail\BaseMail;

class SendEmailProjectApprovalJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

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
            ->subject('Pengajuan project baru dari pengguna SANFXtra!')
            ->leftLogo(asset('assets/svg/sanf-logo-blue.svg'))
            ->rightLogo(asset('assets/svg/sanf-tagline.svg'))
            ->banner(file_get_url(optional($this->project->image_file)->path) ?? asset('assets/svg/email-verification.svg'))
            ->line($this->project->title)
            ->line($this->project->description)
            ->actionApproval([
                [__('Approve Project'), route('projects.approve', ['xid' => $this->project->xid])],
                [__('Reject Project'), route('projects.reject', ['xid' => $this->project->xid])]
            ]);

        return Mail::to($this->emailRecipients)->send($projectApprovalMail);
    }
}
