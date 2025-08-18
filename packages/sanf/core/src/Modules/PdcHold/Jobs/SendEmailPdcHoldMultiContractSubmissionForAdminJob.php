<?php

namespace Sanf\Core\Modules\PdcHold\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Sanf\Core\Mail\MailLayout2Columns;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldTypeEnum;

/**
 * @since CR2025
 */
class SendEmailPdcHoldMultiContractSubmissionForAdminJob implements ShouldQueue
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
            'Tanggal Penundaan' => date_localized($this->data->date_start, '%B %Y'),
        ];

        $tableData = [];
        foreach ($this->data->giros_amount as $contract_no => $amount) {
            $tableData[] = [
                'contract_no' => $contract_no,
                'amount' => 'Rp. ' . number_format($amount, 0, ',', '.'),
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
                    'joinToIndex' => 3,
                    'html' => '<p style="color: #232227; font-size: 14px;"><strong>Daftar Kontrak Anda</strong><p>',
                ],
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
            ->writeTableBody($tableData);

        $ccMails = explode(',', config('sanf-mobile.mail_to.it_helpdesk'));

        return Mail::to($this->recipient)
            ->cc($ccMails)
            ->send($pdcHoldSubmission);
    }
}
