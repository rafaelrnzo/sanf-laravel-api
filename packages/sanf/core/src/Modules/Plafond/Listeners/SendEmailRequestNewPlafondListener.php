<?php

namespace Sanf\Core\Modules\Plafond\Listeners;


use Sanf\Core\Modules\Plafond\SendEmailNewRequestPlafondJob;
use Sanf\Core\Modules\User\Enums\ProfileType;

class SendEmailRequestNewPlafondListener
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
            'Nilai Pengajuan Plafon' => 'Rp. ' . number_format($plafondRequest->amount, 0, ',', '.'),
        ];

        $data = array_filter($data, function ($value) {
            return $value !== null;
        });

        dispatch(new SendEmailNewRequestPlafondJob($data, [$profile->email]));
    }
}
