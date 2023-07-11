<?php

namespace Sanf\Core\Modules\Contract\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Sanf\Core\Mail\MailLayout2Columns;

class SendEmailFinancingUnitLocationJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $data;
    protected $recipient;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($data, $recipient)
    {
        $this->data = $data;
        $this->recipient = $recipient;
    }

    public function handle()
    {
        $simulationEmail = (new MailLayout2Columns())
            ->subject('Pengajuan Perubahan Lokasi Unit Pembiayaan')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->greeting(__('Halo Admin SANFIND!'))
            ->line(
                __(
                    '<blockquote style="margin: 0 0;font-size: 16px; line-height: 150%;">
                    Pengguna atas nama <strong>"' . $this->data['full_name'] . '"</strong> telah mengajukan perubahan untuk lokasi untuk salah satu unit pembiayaan, berikut lampiran detail perubahannya.
                </blockquote>
            '
                )
            )
            ->writeContent($this->data['content'])
            ->generateSeparator([
                ['joinToIndex' => 1, 'html' => '<hr style="border: 1px solid rgba(3, 37, 126, 0.08); margin: 5px 0;">'],
                ['joinToIndex' => 5, 'html' => '<hr style="border: 1px solid rgba(3, 37, 126, 0.08); margin: 5px 0;">'],
                [
                    'joinToIndex' => 6,
                    'html' => '<p style="color: #232227; font-size: 14px;"><strong>Perubahan Lokasi</strong></p>'
                ],
            ]);

        return Mail::to($this->recipient)
            ->cc(config('sanf-mobile.mail_to.it_helpdesk'))
            ->send($simulationEmail);
    }
}
