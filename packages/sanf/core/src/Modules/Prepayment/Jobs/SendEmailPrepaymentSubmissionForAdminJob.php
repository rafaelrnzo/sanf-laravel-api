<?php


namespace Sanf\Core\Modules\Prepayment\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use NbsPhp\Core\Mail\BaseMail;
use Sanf\Core\Modules\Prepayment\Dtos\GetPdfPrepaymentSimulationRequestDto;
use Sanf\Core\Modules\Prepayment\Services\GetPdfPrepaymentSimulationService;


class SendEmailPrepaymentSubmissionForAdminJob implements ShouldQueue
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

    public function handle(GetPdfPrepaymentSimulationService $service)
    {
        $prepayment = (new BaseMail)
            ->subject('Hasil Simulasi Pelunasan Dipercepat')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->greeting(__('Halo Admin SANF!'))
            ->line(__(
                '<p>
                    Pengguna atas nama <strong>“' . $this->data->user->full_name . '“</strong> telah mengajukan pelunasan dipercepat pertanggal
                    <strong>' . Carbon::parse($this->data->created_at)->formatLocalized('%d %B %Y') . '</strong> dari nomor kontrak
                    <strong>“' . $this->data->contract_no . '”</strong>. Berikut lampiran hasil perhitungan pelunasan dipercepat
                    dalam bentuk PDF untuk kepentingan proses selanjutnya Terimakasih.
                </p>'
            ))
            ->lineWithUrl(
                __('Email ini dibuat secara otomatis mohon tidak membalas email ini, jika terdapat keluhan silahkan hubungi'),
                [__('Sanf Customer Service'), '#']
            )
            ->lineWithUrl(
                __('. Jika Anda merasa tidak membuat request tersebut mohon abaikan email ini atau anda dapat'),
                [__('Laporkan email ini'), '#']
            );

        $prepayment->attachData($service->execute(new GetPdfPrepaymentSimulationRequestDto([
            'userId' => $this->data->user_id,
            'contractNo' => $this->data->contract_no,
            'prepaymentDate' => $this->data->prepayment_date,
            'totalPrepayment' => $this->data->total_prepayment,
            'currencyType' => $this->data->currency_type,
            'items' => $this->data->items,
        ])), 'Simulasi Pelunasan Dipercepat ' . date('d_m_y') . '.pdf');

        return Mail::to($this->recipient)->send($prepayment);
    }
}
