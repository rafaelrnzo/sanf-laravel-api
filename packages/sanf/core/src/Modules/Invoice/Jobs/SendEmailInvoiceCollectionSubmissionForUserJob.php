<?php


namespace Sanf\Core\Modules\Invoice\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Sanf\Core\Mail\MailLayout2Columns;
use Sanf\Core\Modules\Invoice\Dtos\GetPdfPrepaymentSimulationRequestDto;
use Sanf\Core\Modules\Invoice\Services\GetPdfPrepaymentSimulationService;


class SendEmailInvoiceCollectionSubmissionForUserJob implements ShouldQueue
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
        setlocale(LC_ALL, 'id_ID.UTF-8', 'id_ID.UTF-8'); // set locale to use local time Indonesia

        $data = [
            'Tanggal Pengajuan' => Carbon::parse($this->data[0]->created_at)->formatLocalized('%A %d %B %Y'),
            'Tanggal Pengambilan' => Carbon::parse($this->data[0]->pickup_date)->formatLocalized('%A %d %B %Y')
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

        $invoiceSubmission = (new MailLayout2Columns())
            ->subject('Pengajuan Pengambilan Invoice')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->greeting(__("Halo {$this->data[0]->user->full_name}!"))
            ->line(__('<p>Berikut kami lampirkan data untuk pengajuan pengambilan invoice untuk pengajuan pembiayaan Anda.</p>'))
            ->writeContent($data)
            ->generateSeparator([
                [
                    'joinToIndex' => 2,
                    'html' => '<p style="text-align: center;">
                        <em>
                            *Pastikan anda hadir pada tanggal yang sudah anda tentukan jika tidak maka pengajuan akan berstatus
                            “Ditolak” dan anda bisa mengambil lagi dengan mengajukan ulang.
                        </em>
                    <p>',
                ],
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
                [__('laporkan email ini'), '#']
            );

        return Mail::to($this->recipient->email)->send($invoiceSubmission);
    }
}
