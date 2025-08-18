<?php

namespace Sanf\Core\Modules\PdcHold\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Sanf\Core\Mail\MailLayout2Columns;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldTypeEnum;

/**
 * @since CR2025
 */
class SendEmailPdcHoldMultiGiroSubmissionForUserJob implements ShouldQueue
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
        $pdcHoldType = new PdcHoldTypeEnum($this->data->type);
        $data = [
            'Jenis Pengajuan' => $pdcHoldType->getLabel(),
            'Tanggal Pengajuan' => date_localized($this->data->created_at, '%A, %d %B %Y'),
            'Tanggal Penundaan' => date_localized($this->data->date_start, '%d %B %Y') . ' - ' . date_localized($this->data->date_end, '%d %B %Y'),
            'Nomor Kontrak' => $this->data->contract_no,
        ];

        $tableData = [];
        foreach ($this->data->giros as $datum) {
            $tableData[] = [
                'pdc_no' => $datum->pdc_no,
                'amount' => 'Rp. ' . number_format($datum->amount, 0, ',', '.'),
                'giro_date' => date_localized($datum->giro_date, '%d %B %Y'),
                'pdc_type' => $datum->pdc_type,
            ];
        }

        $adminMail = config('sanf-mobile.mail_to_admin');
        $reportUrl = "mailto:{$adminMail}?subject=Laporan Pengajuan Hold PDC";
        $invoiceSubmission = (new MailLayout2Columns())
            ->subject('Pengajuan Hold PDC')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->greeting('<p style="text-align: center">' . __("Halo {$this->recipient->fullName}!") . '</p>')
            ->line(__('<p style="text-align: center">Berikut kami lampirkan data untuk pengajuan Hold PDC Anda</p>'))
            ->writeContent($data)
            ->generateSeparator([
                ['joinToIndex' => 4, 'html' => '<p style="color: #232227; font-size: 14px;"><strong>Daftar Kontrak Anda</strong><p>'],
            ])
            ->writeTableHead([
                [
                    'targetData' => 'pdc_no',
                    'label' => 'Nomor Giro',
                ],
                [
                    'targetData' => 'amount',
                    'label' => 'Amount PDC',
                ],
                [
                    'targetData' => 'giro_date',
                    'label' => 'Tanggal PDC',
                ],
                [
                    'targetData' => 'pdc_type',
                    'label' => 'Jenis PDC',
                ],
            ])
            ->writeTableBody($tableData)
            ->line(
                '<p style="text-align: center;">
                        <em>
                            Penerbitan faktur akan diproses dalam waktu 30 hari sejak pengajuan Anda dikirimkan, sesuai dengan
                            ketentuan POJK. Silakan periksa status pengajuan Anda secara berkala di aplikasi SANFind.
                        </em>
                    <p><hr>',
                'outroLines'
            )
            ->lineWithUrl(
                __('Email ini dibuat secara otomatis mohon tidak membalas email ini, jika terdapat keluhan silahkan hubungi'),
                [__('SANF Customer Service'), $reportUrl]
            )
            ->lineWithUrl(
                __('. Jika Anda merasa tidak membuat request tersebut mohon abaikan email ini atau anda dapat'),
                [__('laporkan email ini'), $reportUrl]
            );

        return Mail::to($this->recipient->email)->send($invoiceSubmission);
    }
}
