<?php

namespace Sanf\Core\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

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
        $clientTargetBank = $this->data['clientTargetBank'] ?? [];
        $allocationsTargetBank = $this->data['allocationsTargetBank'] ?? [];

        $sanfCompanyConfig = config('additional.company');
        $sanfName = ($sanfCompanyConfig['company_prefix'] ?? 'PT') . ' ' . ($sanfCompanyConfig['company_name'] ?? 'Surya Artha Nusantara Finance');
        $sanfInitial = $sanfCompanyConfig['company_initials'] ?? 'SANF';

        $mailable = $this->subject('Pengajuan Percepatan Pembayaran')
            ->view('core::mail.html.plafond-disbursement-submitted', [
                'tableHeaders' => $tableHeaders,
                'tableData' => $tableData,
                'clientTargetBank' => $clientTargetBank,
                'allocationsTargetBank' => $allocationsTargetBank,
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

        if (isset($this->data['payment_acc_document']) && !empty($this->data['payment_acc_document'])) {
            $paymentDoc = $this->data['payment_acc_document'];
            $paymentDocPath = $paymentDoc['path'] ?? null;

            if ($paymentDocPath && Storage::disk('minio_post')->exists($paymentDocPath)) {
                $mailable->attachData(
                    Storage::disk('minio_post')->get($paymentDocPath),
                    $paymentDoc['origin_name'] ?? $paymentDoc['file_name'] ?? 'Payment-Acceleration-Document.pdf',
                    [
                        'mime' => $paymentDoc['mime_type'] ?? 'application/pdf',
                    ]
                );
            }
        }

        if (isset($this->data['invoice_documents']) && !empty($this->data['invoice_documents'])) {
            foreach ($this->data['invoice_documents'] as $invoiceDoc) {
                $invoiceDocPath = $invoiceDoc['path'] ?? null;

                if ($invoiceDocPath && Storage::disk('minio_post')->exists($invoiceDocPath)) {
                    $mailable->attachData(
                        Storage::disk('minio_post')->get($invoiceDocPath),
                        $invoiceDoc['origin_name'] ?? $invoiceDoc['file_name'] ?? 'Invoice-Document.pdf',
                        [
                            'mime' => $invoiceDoc['mime_type'] ?? 'application/pdf',
                        ]
                    );
                }
            }
        }

        if (isset($this->data['invoice_photos']) && !empty($this->data['invoice_photos'])) {
            foreach ($this->data['invoice_photos'] as $invoicePhoto) {
                $invoicePhotoPath = $invoicePhoto['path'] ?? null;

                if ($invoicePhotoPath && Storage::disk('minio_post')->exists($invoicePhotoPath)) {
                    $mailable->attachData(
                        Storage::disk('minio_post')->get($invoicePhotoPath),
                        $invoicePhoto['origin_name'] ?? $invoicePhoto['file_name'] ?? 'Invoice-Photo.jpg',
                        [
                            'mime' => $invoicePhoto['mime_type'] ?? 'image/jpeg',
                        ]
                    );
                }
            }
        }

        if (isset($this->data['other_documents']) && !empty($this->data['other_documents'])) {
            foreach ($this->data['other_documents'] as $otherDoc) {
                $otherDocPath = $otherDoc['path'] ?? null;

                if ($otherDocPath && Storage::disk('minio_post')->exists($otherDocPath)) {
                    $mailable->attachData(
                        Storage::disk('minio_post')->get($otherDocPath),
                        $otherDoc['origin_name'] ?? $otherDoc['file_name'] ?? 'Supporting-Document.pdf',
                        [
                            'mime' => $otherDoc['mime_type'] ?? 'application/pdf',
                        ]
                    );
                }
            }
        }

        return $mailable;
    }
}
