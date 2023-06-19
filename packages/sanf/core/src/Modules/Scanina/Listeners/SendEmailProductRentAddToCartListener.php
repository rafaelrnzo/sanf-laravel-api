<?php

namespace Sanf\Core\Modules\Scanina\Listeners;

use Carbon\Carbon;
use Sanf\Api\Modules\Scanina\Jobs\SendEmailProductAddToCartForAdminJob;
use Sanf\Api\Modules\Scanina\Jobs\SendEmailProductAddToCartForUserJob;

class SendEmailProductRentAddToCartListener
{
    public function __construct()
    {
        //
    }

    public function handle($event)
    {
        $request = $event->request;
        $profile = $event->profile;

        $createdAt = Carbon::parse($request->createdAt);
        $startedAt = Carbon::parse($request->startDateAvailable);
        $endedAt = Carbon::parse($request->endDateAvailable);

        // Send array data into email for the content
        $data = [
            'fullName' => $profile->getFullName(),
            'content' => [
                'Tanggal Pengajuan' => date_localized($createdAt, '%d %B %Y'),
                'Kategori' => 'Rental',
                'Nama Kendaraan' => $request->name,
                'Jumlah' => 1,
                'Tanggal Mulai Sewa' => date_localized($startedAt, '%d %B %Y'),
                'Tanggal Selesai Sewa' => date_localized($endedAt, '%d %B %Y'),
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

        $recipients = explode(',', config('scanina-api.recipient'));

        dispatch(new SendEmailProductAddToCartForUserJob($data, [$profile->getEmail()]));
        dispatch(new SendEmailProductAddToCartForAdminJob($data, $recipients));
    }
}
