<?php

namespace Sanf\Core\Modules\Plafond\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Sanf\Core\Mail\MailLayout2Columns;

class SendEmailPlafondDisbursementSubmittedForClientJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $data;
    protected $recipients;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $recipients)
    {
        $this->data = $data;
        $this->recipients = $recipients;
    }

    public function handle()
    {
        $fullName = $this->data['to'];
        unset($this->data['to']);

        $adminMail = config('sanf-mobile.mail_to_admin');
        $reportUrl = "mailto:{$adminMail}?subject=Laporan Pengajuan Pencairan Plafon";
        
        $tableHeaders = [
            ['label' => 'No', 'targetData' => 'no'],
            ['label' => 'Customer', 'targetData' => 'customer'],
            ['label' => 'Tanggal Invoice', 'targetData' => 'tanggal_invoice'],
            ['label' => 'Due Date', 'targetData' => 'due_date'],
            ['label' => 'No Invoice', 'targetData' => 'no_invoice'],
            ['label' => 'DPP', 'targetData' => 'dpp'],
            ['label' => 'PPN', 'targetData' => 'ppn'],
            ['label' => 'PPH23', 'targetData' => 'pph23'],
            ['label' => 'Total', 'targetData' => 'total']
        ];
        
        $tableData = [
            [
                'no' => '1',
                'customer' => 'PT CGS INDONESIA',
                'tanggal_invoice' => '30 Apr 2025',
                'due_date' => '19 July 25',
                'no_invoice' => '5150914-05-24',
                'dpp' => 'Rp. 138,778,000',
                'ppn' => 'Rs. 13,652,662',
                'pph23' => 'Rp 2,775,564',
                'total' => 'Rp. 151,248,238'
            ],
            [
                'no' => '2',
                'customer' => 'PT CGS INDONESIA',
                'tanggal_invoice' => '30 Apr 2025',
                'due_date' => '19 July 25',
                'no_invoice' => '5150915-05-24',
                'dpp' => 'Rp. 138,778,000',
                'ppn' => 'Rs. 13,652,662',
                'pph23' => 'Rp 2,775,564',
                'total' => 'Rp. 151,248,238'
            ]
        ];

        $bankSections = [
            [
                'title' => 'PT Surya Artha Nusantara Finance (SANF)',
                'data' => [
                    'Nomor Rekening' => '6077615704545',
                    'Atas Nama' => 'PT CGS INDONESIA'
                ]
            ],
            [
                'title' => 'BANK PERMATA',
                'data' => [
                    'Nomor Rekening/Virtual Account' => '6876200000447201',
                    'Atas Nama' => 'PT CGS INDONESIA QQ PT Surya Artha Nusantara Finance'
                ]
            ],
            [
                'title' => 'BANK MANDIRI',
                'data' => [
                    'Nomor Rekening/Virtual Account' => '8890253000008472',
                    'Atas Nama' => 'PT CGS INDONESIA QQ PT Surya Artha Nusantara Finance'
                ]
            ]
        ];

        $mailable = (new MailLayout2Columns())
            ->subject('Pengajuan Percepatan Pembayaran')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->greeting(__('Kepada Tim PT MENARA TERUS MAKMUR'))
            ->line(__('Selamat siang,'))
            ->line(__('Sebelumnya kami ucapkan terima kasih atas kerjasama SANF dengan menajadi partner dalam pencairan invoice Financing Supplier dengan PT MENARA TERUS MAKMUR dimasa yang akan datang. Dimana SANF meminta bantuan dalam tabel dibawah ini untuk disetujui dan diversifikasi secepatnya:'))
            ->line(__('<strong>1. Penyesuaian atas Surat Permohonan Percepatan Pembayaran I Terlampir) dari PT CGS INDONESIA sebagai salah satu Supplier PT MENARA TERUS MAKMUR sebelah kami telah menerima dokumen.</strong>'))
            ->line(__('<strong>2. Penyesuaian atas invoice-invoice dengan nilai sebagaimana tercantum dalam tabel dibawah ini telah disetujui dan diversifikasi secepatnya:</strong>'))
            ->writeTableHead($tableHeaders)
            ->writeTableBody($tableData)
            ->line(__('<strong>3. PT Surya Artha Nusantara Finance (SANF) akan melakukan pembayaran invoice diacapati I Detail Nomor 2) kepada rekening Virtual Account dengan rincian sebagai berikut:</strong>'))
            ->writeBankSections($bankSections)
            ->line(__('<strong>4. PT MENARA TERUS MAKMUR akan melakukan pembayaran atas Invoice yang disetujui (Detail Nomor 2) akan akan dibayarkan secara tepat waktu sesuai Tanggal Jatuh Tempo melalui Pembayaran transfer kepada nomor Virtual Account dengan rincian sebagai berikut:</strong>'))
            ->line(__('Demikian permohonan kami atas konfirmasi beberapa persetujuan Invoice Financing PT CGS INDONESIA Terima kasih atas bantuan dan waktunya.'))
            ->line(__('Best regards, Customer Relation'))
            ->line(__('PT. Surya Artha Nusantara Finance'))
            ->line(__('Email ini dibuat secara otomatis mohon tidak membalas email ini, jika terdapat keluhan silahkan hubungi <a href="#">SANF Care</a>. Jika anda merasa tidak membuat request tersebut mohon abaikan email ini atau anda dapat <a href="' . $reportUrl . '">Laporkan email ini</a>.'));

        return Mail::to($this->recipients)->send($mailable);
    }
}
