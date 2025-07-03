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
    protected $pdfAttachment;

    public function __construct(
        array $data,
        string $appUrl,
        ?string $pdfAttachment = null
    ) {
        $this->data = $data;
        $this->appUrl = $appUrl;
        $this->pdfAttachment = $pdfAttachment;
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
            ['label' => 'Total', 'targetData' => 'total'],
        ];

        $tableData = $this->data['invoices'] ?? [];
        $targetBankForSanf = $this->data['targetBankForSanf'] ?? [];
        $targetBankForClient = $this->data['targetBankForClient'] ?? [];

        $sanfCompanyConfig = config('additional.company');
        $sanfName = ($sanfCompanyConfig['company_prefix'] ?? 'PT') . ' ' . ($sanfCompanyConfig['company_name'] ?? 'Surya Artha Nusantara Finance');
        $sanfInitial = $sanfCompanyConfig['company_initials'] ?? 'SANF';

        $mailable = $this->subject('Pengajuan Percepatan Pembayaran')
            ->view('core::mail.html.plafond-disbursement-submitted', [
                'tableHeaders' => $tableHeaders,
                'tableData' => $tableData,
                'targetBankForSanf' => $targetBankForSanf,
                'targetBankForClient' => $targetBankForClient,
                'reportUrl' => $reportUrl,
                'leftLogo' => asset('assets/png/sanf-logo-blue.png'),
                'rightLogo' => asset('assets/png/sanf-tagline.png'),
                'recipientName' => $this->data['fullName'] ?? 'Nama Penerima',
                'clientName' => $this->data['company_info']['company_name'] ?? $this->data['fullName'] ?? 'Nama Client',
                'bowheerName' => $this->data['bowheer']->name ?? 'Nama Bowheer',
                'sanfName' => $sanfName,
                'initialSanf' => $sanfInitial,
                'sanfInitial' => $sanfInitial,
            ]);

        if ($this->pdfAttachment && file_exists($this->pdfAttachment)) {
            $mailable->attach($this->pdfAttachment, [
                'as' => "Surat-Percepatan-Plafond-{$this->data['plafond_id']}.pdf",
                'mime' => 'application/pdf',
            ]);
        }

        return $mailable;
    }
}
