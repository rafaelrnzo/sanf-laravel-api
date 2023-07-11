<?php

namespace Sanf\Core\Modules\Scanina\Listeners;

use Carbon\Carbon;
use Sanf\Api\Modules\Scanina\Jobs\SendEmailProductAddToCartForAdminJob;
use Sanf\Api\Modules\Scanina\Jobs\SendEmailProductAddToCartForUserJob;

class SendEmailProductSparePartAddToCartListener
{

    public function handle($event)
    {
        $request = $event->request;
        $profile = $event->profile;

        $createdAt = Carbon::parse($request->createdAt);

        // Send array data into email for the content
        $data = [
            'fullName' => $profile->getFullName(),
            'content' => [
                'Tanggal Pengajuan' => date_localized($createdAt, '%d %B %Y'),
                'Kategori' => 'Spare Parts',
                'Nama Kendaraan' => $request->name,
                'Jumlah' => $request->quantity,
                'Harga' => 'Rp. ' . number_format($request->price, 0, ',', '.'),
                'Item Number' => $request->itemNumber,
                'Serial Number' => $request->serialNumber,
                'Weight' => "{$request->weight}kg",
                'Dimension' => "{$request->length}cm x {$request->width}cm x {$request->height}cm",
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
