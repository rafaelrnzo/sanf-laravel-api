<?php

namespace Sanf\Core\Modules\Insurance\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Sanf\Core\Mail\MailLayout2Columns;
use Sanf\Core\Modules\User\Entities\ProfileEntityInterface;

class SendEmailInsuranceClaimSubmissionForAdminJob implements ShouldQueue
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
        /** @var ProfileEntityInterface $profile */
        $profile = $this->data->profile;
        $data = [
            'Tanggal Pengajuan' => date_localized($this->data->created_at, '%d %B %Y'),
            'Serial Number' => $this->data->serial_no,
            'No Polis' => $this->data->polis_no,
            'Data Unit' => $this->data->brand_type_model,
            'Tahun Kendaraan' => $this->data->year,
            'Lokasi Pertanggungan' => $this->data->location_metadata->city_name,
            'Tanggal Kejadian' => date_localized($this->data->incident_date, '%d %B %Y'),
            'Keterangan' => $this->data->description,
            'No Telepon PIC' => $profile->getPhoneNumber(),
            'Email PIC' => $profile->getEmail(),
        ];
        $mailable = (new MailLayout2Columns())
            ->subject('Pengajuan Klaim Asuransi ' . $this->data->user->full_name)
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->greeting(__('Halo Admin SANFIND!'))
            ->line(
                __(
                    'Pengguna atas nama <strong>“' . $this->data->user->full_name . '”</strong> telah mengajukan klaim asuransi, berikut kami lampirkan detailnya'
                )
            )
            ->writeContent($data)
            ->generateSeparator([
                ['joinToIndex' => 1, 'html' => '<hr style="border: 1px solid rgba(3, 37, 126, 0.08); margin: 5px 0;">'],
                [
                    'joinToIndex' => 2,
                    'html' => '<p style="color: #232227; font-size: 14px;"><strong>Detail Klaim Asuransi</strong></p>'
                ],
                ['joinToIndex' => 7, 'html' => '<hr style="border: 1px solid rgba(3, 37, 126, 0.08); margin: 5px 0;">'],
            ]);

        foreach ($this->data->image_files as $imageFile) {
            $mailable->attachFromStorage($imageFile->path);
        }

        $ccMails = explode(',', config('sanf-mobile.mail_to.it_helpdesk'));
        return Mail::to($this->recipient)
            ->cc($ccMails)
            ->send($mailable);
    }
}
