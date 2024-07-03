<?php

namespace Sanf\Core\Modules\Plafond\Jobs;

use Dompdf\Dompdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Sanf\Core\Mail\MailLayout2Columns;

class SendEmailPaymentAccelarationDocumentJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $recipient;
    protected $payload;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($recipient, $payload)
    {
        $this->recipient = $recipient;
        $this->payload = $payload;
    }

    public function handle()
    {
        $content = [
            'No Plafond' => $this->payload->plafondId,
            'Perusahaan' => $this->payload->company,
        ];

        $pdfFile = $this->generateFile([
            'client' => '(PT) Pihak Pertama',
            'customer' => '(PT) Pihak Kedua',
            'customer_address' => 'Jl. Alamat Pihak Kedua',
            'document_no' => 'Nomor Surat',
            'document_date' => 'Tanggal Surat',
            'first_signer_company' => '(PT) Pihak Pertama',
            'first_signer_name' => 'Pejabat Pihak Pertama',
            'first_signer_position' => 'Jabatan Pihak Pertama',
            'second_signer_company' => '(PT) Pihak Kedua',
            'second_signer_name' => 'Pejabat Pihak Kedua',
            'second_signer_position' => 'Jabatan Pihak Kedua',
            'invoices' => [
                [
                    'index' => '1.',
                    'no' => 'xxxx-xxxx/xxxx/xxxx',
                    'date' => 'dd/mm/yyyy',
                    'amount' => 'Rp xxx.xxx,xx',
                    'vat_amount' => 'Rp xxx.xxx,xx',
                    'tax_amount' => 'Rp xxx.xxx,xx',
                    'backharge_amount' => 'Rp xxx.xxx,xx',
                    'total_amount' => 'Rp xxx.xxx,xx',
                ],
            ],
            'total_amount' => 'Rp xxx.xxx,xx',
        ]);

        $tempFilePath = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($tempFilePath, $pdfFile);

        $adminMail = config('sanf-mobile.mail_to_admin');
        $reportUrl = "mailto:{$adminMail}?subject=Laporan Pengajuan Surat Percepatan Pencairan Plafon";
        $mailable = (new MailLayout2Columns())
            ->subject('Pengajuan Surat Percepatan Pencairan Plafon')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->greeting(__('Halo :name!', ['name' => $this->payload->company]))
            ->line(__(
                '<blockquote style="margin: 0 0;font-size: 16px; line-height: 150%;">
                    Terdapat permintaan Pengajuan Surat Percepatan Pencairan Plafond, berikut kami lampirkan dokumennya untuk Anda.
                </blockquote>
            '
            ))
            ->writeContent($content)
            ->generateSeparator([
                ['joinToIndex' => 0, 'html' => '<hr style="border: 1px solid rgba(3, 37, 126, 0.08);">'],
            ])
            ->line(
                __('Email ini dibuat secara otomatis mohon tidak membalas email ini, jika terdapat keluhan silahkan hubungi Sanf Customer Service')
            )
            ->lineWithUrl(
                __('. Jika Anda merasa tidak membuat request tersebut mohon abaikan email ini atau anda dapat'),
                [__('Laporkan email ini'), $reportUrl]
            )
            ->attach($tempFilePath, [
                'as' => "Surat-Percepatan-Plafond:{$this->payload->plafondId}.pdf",
                'mime' => 'application/pdf',
            ]);

        return Mail::to([$this->recipient, 'diar@nusantarabetastudio.com', 'muflih@nusantarabetastudio.com'])->send($mailable);
    }

    private function generateFile(array $content): string
    {
        $template = view('acc-document-template', ['content' => $content])->render();

        $pdf = new Dompdf();
        $pdf->loadHtml($template);
        $pdf->setPaper('A4', 'potrait');
        $pdf->render();

        return $pdf->output();
    }
}
