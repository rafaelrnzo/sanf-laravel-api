<?php


namespace Sanf\Core\Modules\Financing;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Sanf\Core\Mail\MailLayout2Columns;

class SendEmailFinancingApplicationForAdminJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    protected $user;

    protected $emailRecipients;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($data, $user, $emailRecipients)
    {
        $this->data = $data;
        $this->user = $user;
        $this->emailRecipients = $emailRecipients;
    }

    public function handle()
    {
        $mailable = (new MailLayout2Columns())
            ->subject('Pengajuan Pembiayaan Baru')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->greeting(__('Halo Admin SANFIND!'))
            ->line(
                __(
                    '<blockquote style="margin: 0 0;font-size: 16px; line-height: 150%;">Berikut lampiran ringkasan pengajuan pembiayaan ' . $this->user->full_name . '.</blockquote>'
                )
            )
            ->writeContent($this->data)
            ->generateSeparator([
                ['joinToIndex' => 1, 'html' => '<hr style="border: 1px solid rgba(3, 37, 126, 0.08);">'],
            ]);

        return Mail::to($this->emailRecipients)->send($mailable);
    }
}
