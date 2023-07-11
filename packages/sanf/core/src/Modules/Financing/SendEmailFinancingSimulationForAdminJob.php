<?php

namespace Sanf\Core\Modules\Financing;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use NbsPhp\Core\Mail\BaseMail;
use Sanf\Core\Modules\Financing\Services\GetPdfFinancingSimulationService;

class SendEmailFinancingSimulationForAdminJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
            ->greeting("Halo Admin SANFIND!")
            ->line(
                __(
                    '<blockquote style="margin: 0 3em;font-size: 16px; line-height: 150%;">Berikut kami lampirkan hasil perhitungan simulasi pengajuan pembiayaan ' . $this->recipient->name . '</blockquote> '
                )
            );

        $simulationEmail->attachData(
            $service->execute($this->data),
            'SANFIND-Simulasi-' . date('Y-m-d-H-i-s') . '.pdf'
        );

        return Mail::to($this->recipient->email)
            ->cc(config('sanf-mobile.mail_to.it_helpdesk'))
            ->send($simulationEmail);
    }
}
