<?php


namespace Sanf\Core\Modules\Invoice\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Sanf\Core\Mail\MailLayout2Columns;
use Sanf\Core\Modules\Invoice\Dtos\GetPdfPrepaymentSimulationRequestDto;
use Sanf\Core\Modules\Invoice\Services\GetPdfPrepaymentSimulationService;


class SendEmailInvoiceCollectionSubmissionForAdminJob implements ShouldQueue
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
            'Tanggal Pengajuan' => date_localized($this->data[0]->created_at),
            'Tanggal Pengambilan' => date_localized($this->data[0]->pickup_date),
        ];

        $tableData = [];
        foreach ($this->data as $datum) {
            $tableData[] = [
                'contract_number' => $datum->contract_no,
                'model' => $datum->brand_type_model,
                'serial_number' => $datum->serial_no,
                'year' => $datum->year
            ];
        }

        $adminMail = config('sanf-mobile.mail_to_admin');
        $reportUrl = "mailto:{$adminMail}?subject=Laporan Pengajuan Pengambilan Invoice";
        $invoiceSubmission = (new MailLayout2Columns())
            ->subject('Pengajuan Pengambilan Invoice')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->greeting(__('Halo Admin SANFIND'))
            ->line(__('
                <p>
                    Pengguna atas nama <strong>“' . $this->data[0]->user->full_name . '”</strong> telah mengajukan pengambilan invoice,
                    berikut kami lampirkan detailnya
                </p>'
            ))
            ->writeContent($data)
            ->generateSeparator([
                ['joinToIndex' => 2, 'html' => '<hr style="border: 1px solid rgba(3, 37, 126, 0.08); margin: 5px 0;">'],
                ['joinToIndex' => 3, 'html' => '<p style="color: #232227; font-size: 14px;"><strong>Daftar Pengembalian Invoice</strong><p>'],
            ])
            ->writeTableHead([
                [
                    'targetData' => 'contract_number',
                    'label' => 'No Kontrak',
                ],
                [
                    'targetData' => 'model',
                    'label' => 'Model'
                ],
                [
                    'targetData' => 'serial_number',
                    'label' => 'Serial Number'
                ],
                [
                    'targetData' => 'year',
                    'label' => 'Tahun'
                ]
            ])
            ->writeTableBody($tableData)
            ->lineWithUrl(
                __('Email ini dibuat secara otomatis mohon tidak membalas email ini, jika terdapat keluhan silahkan hubungi'),
                [__('Sanf Customer Service'), '#']
            )
            ->lineWithUrl(
                __('. Jika Anda merasa tidak membuat request tersebut mohon abaikan email ini atau anda dapat'),
                [__('laporkan email ini'), $reportUrl]
            );

        $recipients = explode(',', config('sanf-mobile.mail_to_admin'));

        return Mail::to($recipients)->send($invoiceSubmission);
    }
}
