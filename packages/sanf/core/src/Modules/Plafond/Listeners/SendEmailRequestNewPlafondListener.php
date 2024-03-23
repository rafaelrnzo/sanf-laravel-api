<?php

namespace Sanf\Core\Modules\Plafond\Listeners;

use Sanf\Core\Modules\Plafond\SendEmailNewRequestPlafondForAdminJob;
use Sanf\Core\Modules\Plafond\SendEmailNewRequestPlafondForUserJob;
use Sanf\Core\Modules\User\Enums\ProfileType;

class SendEmailRequestNewPlafondListener
{
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
            'fullName' => $profile->fullName,
            'type' => $plafondRequest->type,
            'Tanggal Pengajuan' => date_localized($plafondRequest->createdAt, '%d %B %Y'),
            'Nama Customer' => ($profile->typeId === ProfileType::PERSONAL) ? $profile->fullName : null,
            'Nama PIC' => ($profile->typeId === ProfileType::COMPANY) ? $profile->picName : null,
            'Nama Perusahaan' => $profile->fullName,
            'Customer ID' => $plafondRequest->profileXid,
            'Nilai Pengajuan Plafon' => 'Rp. ' . number_format($plafondRequest->amount, 0, ',', '.'),
        ];

        $data = array_filter($data, function ($value) {
            return $value !== null;
        });

        $recipients = explode(',', config('sanf-mobile.mail_to.marketing'));

        dispatch(new SendEmailNewRequestPlafondForUserJob($data, [$profile->email]));
        dispatch(new SendEmailNewRequestPlafondForAdminJob($data, $recipients));
    }
}
