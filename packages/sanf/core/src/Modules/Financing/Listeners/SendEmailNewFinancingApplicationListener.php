<?php

namespace Sanf\Core\Modules\Financing\Listeners;


use Sanf\Core\Modules\Financing\SendEmailFinancingApplicationForAdminJob;
use Sanf\Core\Modules\Financing\SendEmailFinancingApplicationForUserJob;
use Sanf\Core\Modules\User\Enums\ProfileType;

class SendEmailNewFinancingApplicationListener
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
        $financingApplication = $event->financingApplication;
        $profile = $financingApplication->profile;

        // COMPANY
        if ($profile->typeId == ProfileType::COMPANY) {
            $data = [
                'Tanggal Pengajuan' => date_localized($financingApplication->created_at, '%d %B %Y'),
                'Nomor Pengajuan' => $financingApplication->application_code,
                'Nama PIC' => $profile->picName,
                'Nama Perusahaan' => $profile->fullName,
                'Email PIC' => $profile->email,
                'Jenis Fasilitas Pembiayaan' => optional($financingApplication->facility)->name,
                'Cara Pembayaran' => optional($financingApplication->method)->name
            ];
        } elseif ($profile->typeId == ProfileType::PERSONAL) {
            $data = [
                'Tanggal Pengajuan' => date_localized($financingApplication->created_at, '%d %B %Y'),
                'Nomor Pengajuan' => $event->financingApplication->application_code,
                'Nama' => $profile->fullName,
                'Email' => $profile->email,
                'Jenis Fasilitas Pembiayaan' => optional($event->financingApplication->facility)->name,
                'Cara Pembayaran' => optional($event->financingApplication->method)->name
            ];
        } else {
            throw new \Exception('Invalid Profile Type');
        }

        $data = array_filter($data, function ($value) {
            return $value !== null;
        });

        $recipients = explode(',', config('sanf-mobile.mail_to_admin'));
        dispatch(new SendEmailFinancingApplicationForAdminJob($data, $financingApplication->user, $recipients));
        dispatch(new SendEmailFinancingApplicationForUserJob($data, $financingApplication->user, $profile->email));
    }
}
