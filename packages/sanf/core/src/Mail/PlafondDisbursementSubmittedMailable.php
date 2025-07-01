<?php

namespace Sanf\Core\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PlafondDisbursementSubmittedMailable extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;
    protected $appUrl;

    public function __construct(
        array $data,
        string $appUrl
    ) {
        $this->data = $data;
        $this->appUrl = $appUrl;
    }

    public function build()
    {
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

        return $this->subject('Pengajuan Percepatan Pembayaran')
            ->view('core::mail.html.plafond-disbursement-submitted', [
                'data' => $this->data,
                'appUrl' => $this->appUrl,
                'tableHeaders' => $tableHeaders,
                'tableData' => $tableData,
                'bankSections' => $bankSections,
                'reportUrl' => $reportUrl,
                'leftLogo' => asset('assets/png/sanf-logo-blue.png'),
                'rightLogo' => asset('assets/png/sanf-tagline.png'),
            ]);
    }
}