<?php

namespace Sanf\Core\Modules\Plafond\Listeners;


use Sanf\Core\Modules\Plafond\SendEmailRequestIncreasePlafondJob;
use Sanf\Core\Modules\User\Enums\ProfileType;

class SendEmailRequestIncreasePlafondListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        $plafondRequest = $event->plafondRequest;
        $profile = $event->profile;

        // Send array data into email for the content
        $data = [
            'Tanggal Pengajuan' => date_localized($plafondRequest->createdAt),
            'Nama Customer' => ($profile->typeId === ProfileType::PERSONAL) ? $profile->fullName : null,
            'Nama PIC' => ($profile->typeId === ProfileType::COMPANY) ? $profile->picName : null,
            'Nama Perusahaan' => $profile->fullName,
            'Nilai Plafon Saat Ini' => 'Rp. ' . number_format($plafondRequest->currentBalance, 0, ',', '.'),
            'Nilai Plafon Tambahan' => 'Rp. ' . number_format($plafondRequest->addedBalance, 0, ',', '.'),
            '<p style="color: #232227;">
                <b>Total Plafon Anda</b>
            </p>' => '<b>Rp. ' . number_format($plafondRequest->submittedBalance, 0, ',', '.') . '</b>',
        ];

        $data = array_filter($data, function ($value) {
            return $value !== null;
        });

        $recipients = explode(',', config('sanf-mobile.mail_to_admin'));

        dispatch(new SendEmailRequestIncreasePlafondJob($data, $recipients));
    }
}
