<?php

namespace Sanf\Core\Modules\Scanina\Listeners;

use Carbon\Carbon;
use Sanf\Api\Modules\Scanina\Jobs\SendEmailProductAddToCartForAdminJob;
use Sanf\Api\Modules\Scanina\Jobs\SendEmailProductAddToCartForUserJob;

class SendEmailProductRentAddToCartListener
{

    public function handle($event)
    {
        $request = $event->request;
        $profile = $event->profile;

        $createdAt = Carbon::parse($request->createdAt);
        $startedAt = Carbon::parse($request->startDateAvailable);
        $endedAt = Carbon::parse($request->endDateAvailable);

        // Send array data into email for the content
        $dateFormat = '%d %B %Y';
        $data = [
            'fullName' => $profile->getFullName(),
            'content' => [
                'Tanggal Pengajuan' => date_localized($createdAt, "{$dateFormat}"),
                'Kategori' => 'Rental',
                'Nama Kendaraan' => $request->name,
                'Jumlah' => 1,
                'Tanggal Mulai Sewa' => date_localized($startedAt, $dateFormat),
                'Tanggal Selesai Sewa' => date_localized($endedAt, $dateFormat),
                'Harga' => 'Rp. ' . number_format($request->price, 0, ',', '.'),
                'Item Number' => $request->itemNumber,
                'Serial Number' => $request->serialNumber,
                'Tahun' => $request->year,
                'Pemilik Kendaraan' => $request->provider ?? 'Scanina',
            ],
        ];

        $data = array_filter($data, function ($value) {
            return $value !== null;
        });

        $sanfMailAdmin = explode(',', config('sanf-mobile.mail_to.marketing'));
        $scaninaMailAdmin = explode(',', config('scanina-api.recipient'));
        $recipients = array_merge($sanfMailAdmin, $scaninaMailAdmin);

        dispatch(new SendEmailProductAddToCartForUserJob($data, [$profile->getEmail()]));
        dispatch(new SendEmailProductAddToCartForAdminJob($data, $recipients));
    }
}
