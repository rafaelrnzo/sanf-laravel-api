<?php

namespace Sanf\Core\Modules\PdcHold\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Sanf\Core\Mail\MailLayout2Columns;
use Sanf\Core\Modules\Contract\Enums\CurrencyTypeEnum;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldTypeEnum;

/**
 * @since CR2025
 */
class SendEmailPdcHoldMultiContractSubmissionForUserJob implements ShouldQueue
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
        $pdcHoldType = new PdcHoldTypeEnum($this->data->type);
        $data = [
            'Jenis Pengajuan' => $pdcHoldType->getLabel(),
            'Tanggal Pengajuan' => date_localized($this->data->created_at, '%A, %d %B %Y'),
            'Tanggal Penundaan' => date_localized($this->data->date_start, '%B %Y'),
            'Alasan' => $this->data->reason_value,
        ];

        $tableData = [];
        foreach ($this->data->giros_amount as $currency_type => $contracts) {
            foreach ($contracts as $contract_no => $amount) {
                $currencyType = CurrencyTypeEnum::search($currency_type);
                $currency = $currencyType ? (CurrencyTypeEnum::from($currencyType)->getSymbol() ?? $currency_type) : $currency_type;
                $tableData[] = [
                    'contract_no' => $contract_no,
                    'amount' => $currency . ' ' . number_format($amount, 0, ',', '.'),
                ];
            }
        }

        $adminMail = config('sanf-mobile.mail_to_admin');
        $reportUrl = "mailto:{$adminMail}?subject=Laporan Pengajuan Hold PDC";
        $invoiceSubmission = (new MailLayout2Columns())
            ->subject('Pengajuan Hold PDC')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->greeting('<p style="text-align: center">' . __("Halo {$this->recipient->fullName}!") . '</p>')
            ->line(__('<p style="text-align: center">Berikut kami lampirkan data untuk pengajuan Hold PDC Anda</p>'))
            ->writeContent($data)
            ->generateSeparator([
                ['joinToIndex' => 4, 'html' => '<p style="color: #232227; font-size: 14px;"><strong>Daftar Kontrak Anda</strong><p>'],
            ])
            ->writeTableHead([
                [
                    'targetData' => 'contract_no',
                    'label' => 'Nomor Kontrak',
                ],
                [
                    'targetData' => 'amount',
                    'label' => 'Jumlah Giro',
                ],
            ])
            ->writeTableBody($tableData)
            ->line(
                '<p style="text-align: center;">
                        <em>
                            Penerbitan faktur akan diproses dalam waktu 30 hari sejak pengajuan Anda dikirimkan, sesuai dengan
                            ketentuan POJK. Silakan periksa status pengajuan Anda secara berkala di aplikasi SANFind.
                        </em>
                    <p><hr>',
                'outroLines'
            )
            ->lineWithUrl(
                __('Email ini dibuat secara otomatis mohon tidak membalas email ini, jika terdapat keluhan silahkan hubungi'),
                [__('SANF Customer Service'), $reportUrl]
            )
            ->lineWithUrl(
                __('. Jika Anda merasa tidak membuat request tersebut mohon abaikan email ini atau anda dapat'),
                [__('laporkan email ini'), $reportUrl]
            );

        return Mail::to($this->recipient->email)->send($invoiceSubmission);
    }
}
