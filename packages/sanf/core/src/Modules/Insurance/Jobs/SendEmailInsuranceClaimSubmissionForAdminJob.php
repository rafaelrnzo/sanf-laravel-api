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
    protected array $ccMails;
    protected bool $isDefaultCity = false;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $recipient, $ccMails = [], $isDefaultCity = false)
    {
        $this->data = $data;
        $this->recipient = $recipient;
        $this->ccMails = $ccMails;
        $this->isDefaultCity = $isDefaultCity;
    }

    public function handle()
    {
        /** @var ProfileEntityInterface $profile */
        $profile = $this->data->profile;
        $data = [
            'Serial Number' => $this->data->serial_no,
            'Tahun Kendaraan' => $this->data->year,
            'Data Unit' => $this->data->brand_type_model,
            'No Polis' => $this->data->polis_no,
            'Nama Customer' => $this->data->user->full_name,
            'Lokasi Kejadian' => $this->data->location_metadata->city_name,
            'Tanggal Kejadian' => date_localized($this->data->incident_date, '%d %B %Y'),
            'Nama PIC' => $this->data->pic_name,
            'No.Handphone PIC' => $this->data->pic_phone_number,
            'Email PIC' => $profile->getEmail(),
            'Keterangan' => $this->data->description,
            // 'Tanggal Pengajuan' => date_localized($this->data->created_at, '%d %B %Y'),
            // 'No Telepon PIC' => $profile->getPhoneNumber(),
        ];
        $mailable = (new MailLayout2Columns())
            ->subject('Pengajuan Klaim Asuransi ' . $this->data->user->full_name)
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->writeContent($data)
            ->generateSeparator([
                [
                    'joinToIndex' => 0,
                    'html' => '<p style="color: #232227; font-size: 14px;"><strong>Detail Klaim Asuransi</strong></p>',
                ],
                ['joinToIndex' => 8, 'html' => '<hr style="border: 1px solid rgba(3, 37, 126, 0.08); margin: 5px 0;">'],
            ]);

        if ($this->isDefaultCity) {
            $adminMail = config('sanf-mobile.mail_to_admin');
            $reportUrl = "mailto:{$adminMail}?subject=Laporan Pengajuan Klaim Asuransi";

            $mailable = $mailable->greeting('Dengan hormat,')
                ->line('Bersama email ini, kami informasikan bahwa pengguna atas nama <strong>' . $this->data->user->full_name . '</strong> telah mengajukan klaim asuransi, berikut kami lampirkan detail informasi dibawah ini:')
                ->lineWithUrl(
                    __('Email ini dibuat secara otomatis mohon tidak membalas email ini, jika terdapat keluhan silahkan hubungi'),
                    [__('SANF Customer Service'), $reportUrl]
                )
                ->lineWithUrl(
                    __('. Jika Anda merasa tidak membuat request tersebut mohon abaikan email ini atau anda dapat'),
                    [__('Laporkan email ini'), $reportUrl]
                );
        } else {
            $mailable = $mailable->greeting(__('Halo Admin SANFIND!'))
                ->line(
                    __(
                        'Pengguna atas nama <strong>“' . $this->data->user->full_name . '”</strong> telah mengajukan klaim asuransi, berikut kami lampirkan detailnya'
                    )
                );
        }

        foreach ($this->data->image_files as $imageFile) {
            $mailable->attachFromStorage($imageFile->path);
        }

        $ccMails = $this->ccMails ?: ($this->isDefaultCity ? explode(',', config('sanf-mobile.mail_to.it_helpdesk')) : []);

        return Mail::to($this->recipient)
            ->cc($ccMails)
            ->send($mailable);
    }
}
