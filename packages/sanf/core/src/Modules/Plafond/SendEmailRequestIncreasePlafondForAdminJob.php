<?php

namespace Sanf\Core\Modules\Plafond;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Sanf\Core\Mail\MailLayout2Columns;

class SendEmailRequestIncreasePlafondForAdminJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $data;

    protected $emailRecipients;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $emailRecipients)
    {
        $this->data = $data;
        $this->emailRecipients = $emailRecipients;
    }

    public function handle()
    {
        $fullName = $this->data['fullName'];
        $type = $this->data['type'];

        unset($this->data['fullName']);
        unset($this->data['type']);

        $mailable = (new MailLayout2Columns())
            ->subject("Naikan Nilai Plafon {$type}")
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->greeting(__('Halo Admin SANFIND!'))
            ->line(
                __(
                    '<blockquote style="margin: 0 0;font-size: 16px; line-height: 150%;">Berikut lampiran ringkasan Pengajuan Plafon ' . "{$type} {$fullName}" . '</blockquote> '
                )
            )
            ->writeContent($this->data)
            ->generateSeparator([
                ['joinToIndex' => 1, 'html' => '<hr style="border: 1px solid rgba(3, 37, 126, 0.08);">'],
                [
                    'joinToIndex' => 6,
                    'html' => '<p style="font-size: 16px; font-weight: 700; color:#232227;">Ringkasan Kenaikan Nilai Plafon ' . $type . '</p>',
                ],
                ['joinToIndex' => 7, 'html' => '<hr style="border: 1px solid rgba(3, 37, 126, 0.08);">'],
                ['joinToIndex' => 10, 'html' => '<hr style="border: 1px solid rgba(3, 37, 126, 0.08);">'],
            ]);

        $ccMails = explode(',', config('sanf-mobile.mail_to.it_helpdesk'));

        return Mail::to($this->emailRecipients)
            ->cc($ccMails)
            ->send($mailable);
    }
}
