<?php


namespace Sanf\Core\Modules\Insurance\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Sanf\Core\Mail\MailLayout2Columns;


class SendEmailInsuranceClaimSubmissionForUserJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
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
        $data = [
            'Tanggal Pengajuan' => date_localized($this->data->created_at),
            'Serial Number' => $this->data->serial_no,
            'No Polisi' => $this->data->polis_no,
            'Data Unit' => $this->data->brand_type_model,
            'Tahun Kendaraan' => $this->data->year,
            'Lokasi Pertanggungan' => $this->data->location_metadata->city_name,
            'Tanggal Kejadian' => date_localized($this->data->incident_date, '%d/%m/%Y'),
            'Keterangan' => $this->data->description,
        ];

        $mailable = (new MailLayout2Columns)
            ->subject('Pengajuan Klaim Asuransi')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->greeting(__("Halo {$this->data->user->full_name}!"))
            ->line(__('Berikut kami lampirkan data untuk pengajuan klaim asuransi Anda untuk'
            ))
            ->writeContent($data)
            ->generateSeparator([
                ['joinToIndex' => 1, 'html' => '<hr style="border: 1px solid rgba(3, 37, 126, 0.08); margin: 5px 0;">'],
                ['joinToIndex' => 2, 'html' => '<p style="color: #232227; font-size: 14px;"><strong>Detail Klaim Asuransi</strong></p>'],
                ['joinToIndex' => 7, 'html' => '<hr style="border: 1px solid rgba(3, 37, 126, 0.08); margin: 5px 0;">'],
            ])
            ->lineWithUrl(
                __('Email ini dibuat secara otomatis mohon tidak membalas email ini, jika terdapat keluhan silahkan hubungi'),
                [__('Sanf Customer Service'), '#']
            )
            ->lineWithUrl(
                __('. Jika Anda merasa tidak membuat request tersebut mohon abaikan email ini atau anda dapat'),
                [__('Laporkan email ini'), '#']
            );

        foreach ($this->data->image_files as $imageFile){
            $mailable->attachFromStorage($imageFile->path);
        }

        return Mail::to($this->recipient->email)->send($mailable);
    }
}
