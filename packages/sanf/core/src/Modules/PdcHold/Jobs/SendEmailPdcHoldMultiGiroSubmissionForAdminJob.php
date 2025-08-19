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
class SendEmailPdcHoldMultiGiroSubmissionForAdminJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $data;
    protected $user;
    protected $recipient;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $user, $recipient)
    {
        $this->data = $data;
        $this->user = $user;
        $this->recipient = $recipient;
    }

    public function handle()
    {
        $pdcHoldType = new PdcHoldTypeEnum($this->data->type);
        $data = [
            'Jenis Pengajuan' => $pdcHoldType->getLabel(),
            'Tanggal Pengajuan' => date_localized($this->data->created_at, '%A, %d %B %Y'),
            'Tanggal Penundaan' => date_localized($this->data->date_start, '%d %B %Y') . ' - ' . date_localized($this->data->date_end, '%d %B %Y'),
            'Nomor Kontrak' => $this->data->contract_no,
        ];

        $tableData = [];
        foreach ($this->data->giros as $datum) {
            $currencyType = CurrencyTypeEnum::search($datum->currency_type);
            $currency = $currencyType ? (CurrencyTypeEnum::from($currencyType)->getSymbol() ?? $datum->currency_type) : $datum->currency_type;

            $tableData[] = [
                'pdc_no' => $datum->pdc_no,
                'amount' => $currency . ' ' . number_format($datum->amount, 0, ',', '.'),
                'giro_date' => date_localized($datum->giro_date, '%d %B %Y'),
                'pdc_type' => $datum->pdc_type,
            ];
        }

        $pdcHoldSubmission = (new MailLayout2Columns())
            ->subject('Pengajuan Hold PDC')
            ->leftLogo(asset('assets/png/sanf-logo-blue.png'))
            ->rightLogo(asset('assets/png/sanf-tagline.png'))
            ->banner(asset('assets/png/email-verification.png'))
            ->greeting('<p style="text-align: center">' . __('Halo Admin SANFIND!') . '</p>')
            ->line(
                __(
                    '
                <p style="text-align: center">
                    Pengguna atas nama <strong>“' . $this->user->fullName . '”</strong> telah mengajukan hold PDC, 
                    berikut kami lampirkan detailnya
                </p>'
                )
            )
            ->writeContent($data)
            ->generateSeparator([
                [
                    'joinToIndex' => 4,
                    'html' => '<p style="color: #232227; font-size: 14px;"><strong>Daftar Giro Anda</strong><p>',
                ],
            ])
            ->writeTableHead([
                [
                    'targetData' => 'pdc_no',
                    'label' => 'Nomor Giro',
                ],
                [
                    'targetData' => 'amount',
                    'label' => 'Amount PDC',
                ],
                [
                    'targetData' => 'giro_date',
                    'label' => 'Tanggal PDC',
                ],
                [
                    'targetData' => 'pdc_type',
                    'label' => 'Jenis PDC',
                ],
            ])
            ->writeTableBody($tableData);

        $ccMails = explode(',', config('sanf-mobile.mail_to.it_helpdesk'));

        return Mail::to($this->recipient)
            ->cc($ccMails)
            ->send($pdcHoldSubmission);
    }
}
