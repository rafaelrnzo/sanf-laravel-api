<?php

namespace Sanf\Api\Modules\Simulation;

use Dompdf\Dompdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use NbsPhp\Core\Mail\BaseMail;

class SendSimulationJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $emailRecipients;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($email, $emailRecipients)
    {
        $this->email = $email;
        $this->emailRecipients = $emailRecipients;
    }

    private function toBase64($assetPath)
    {
        $type = pathinfo($assetPath, PATHINFO_EXTENSION);
        $data = file_get_contents($assetPath);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }

    private function generatePDF()
    {
        $this->email['images'] = [
            0 => $this->toBase64('assets/png/sanf-logo-blue.png'),
            1 => $this->toBase64('assets/png/sanf-tagline.png'),
        ];

        $template = view('vendor/pdf/PDFView', ['contents' => $this->email])->render();

        $pdf = new Dompdf();
        $pdf->loadHtml($template);
        $pdf->setPaper('A4', 'potrait');
        $pdf->render();
        $output = $pdf->output();
        $fileName = 'SANF-Simulasi-' . date('Y-m-d-h-m');

        file_put_contents("temp/$fileName.pdf", $output);
        $path = "temp/$fileName.pdf";

        return $path;
    }

    public function handle()
    {

        $simulationEmail = (new BaseMail())
            ->subject('Hasil Simulasi Pembiayaan')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->line(__(
                'Halo '. $this->email['name'] .'!.
                <br />
                <blockquote style="margin: 0 3em;font-size: 16px; line-height: 150%;">
                    Berikut kami lampirkan hasil perhitungan simulasi pengajuan pembiayaan anda
                </blockquote>
            '))
            ->lineWithUrl(
                __('Email ini dibuat secara otomatis mohon tidak membalas email ini, jika terdapat keluhan silahkan hubungi'),
                [__('Sanf Customer Service'), '#']
            )
            ->lineWithUrl(
                __('. Jika Anda merasa tidak membuat request tersebut mohon abaikan email ini atau anda dapat'),
                [__('Laporkan email ini'), '#']
            );

        $attachment = $this->generatePDF();

        $simulationEmail->attach(public_path($attachment));

        $simulationEmail->from($this->email['email'], $this->email['name']);

        return Mail::to($this->emailRecipients)->send($simulationEmail);
    }
}
