<?php

namespace Sanf\Core\Modules\Contract\Listeners;

use Illuminate\Support\Str;
use Sanf\Core\Modules\Contract\Jobs\SendEmailFinancingUnitLocationJob;

class SendEmailRequestChangeFinancingUnitLocationListener
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        $recipients = explode(',', config('sanf-mobile.mail_to_admin'));


        $createdAt = date_localized($event->submission->created_at);
        $oldLocation = Str::title($event->submission->location_metadata->city_name);
        $newLocation = Str::title($event->submission->submitted_location_metadata->city_name);
        $data['content'] = [
            '<strong>Tanggal Pengajuan</strong>' => "<strong>{$createdAt}</strong>",
            'Nama Unit' => "<strong>{$event->submission->brand_type_model}</strong>",
            'Serial Number' => "<strong>{$event->submission->serial_no}</strong>",
            'Tahun Kendaraan' => "<strong>{$event->submission->year}</strong>",
            'Lokasi Lama' => "<strong>{$oldLocation}</strong>",
            'Lokasi Baru' => "<strong>{$newLocation}</strong>",
        ];

        $data['full_name'] = Str::title($event->submission->user->full_name);

        dispatch(new SendEmailFinancingUnitLocationJob($data, $recipients));
    }
}
