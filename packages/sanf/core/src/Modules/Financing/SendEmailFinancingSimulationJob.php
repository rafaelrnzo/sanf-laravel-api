<?php


namespace Sanf\Core\Modules\Financing;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use NbsPhp\Core\Mail\BaseMail;
use Sanf\Core\Modules\Financing\Services\GetPdfFinancingSimulationService;


class SendEmailFinancingSimulationJob implements ShouldQueue
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

    public function handle(GetPdfFinancingSimulationService $service)
    {
        $simulationEmail = (new BaseMail())
            ->subject('Hasil Simulasi Pembiayaan')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->line(__(
                'Halo ' . $this->recipient->name . '!.
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

        $simulationEmail->attachData($service->execute($this->data), 'SANFIND-Simulasi' . date('Y-m-d-H-i-s') . '.pdf');

        return Mail::to($this->recipient->email)->send($simulationEmail);
    }
}
