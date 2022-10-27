<?php

namespace Sanf\Core\Modules\Plafond\Listeners;

use Sanf\Core\Modules\Plafond\SendEmailNewRequestPlafondForAdminJob;
use Sanf\Core\Modules\Plafond\SendEmailNewRequestPlafondForUserJob;
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
            'name' => ($profile->typeId === ProfileType::PERSONAL) ? $profile->fullName : $profile->picName,
            'Tanggal Pengajuan' => date_localized($plafondRequest->createdAt, '%d %B %Y'),
            'Nama Customer' => ($profile->typeId === ProfileType::PERSONAL) ? $profile->fullName : null,
            'Nama PIC' => ($profile->typeId === ProfileType::COMPANY) ? $profile->picName : null,
            'Nama Perusahaan' => $profile->fullName,
            'Nilai Pengajuan Plafon' => 'Rp. ' . number_format($plafondRequest->amount, 0, ',', '.'),
        ];

        $data = array_filter($data, function ($value) {
            return $value !== null;
        });

        $recipients = explode(',', config('sanf-mobile.mail_to_admin'));

        dispatch(new SendEmailNewRequestPlafondForUserJob($data, [$profile->email]));
        dispatch(new SendEmailNewRequestPlafondForAdminJob($data, $recipients));
    }
}
