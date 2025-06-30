<?php

namespace Sanf\Core\Modules\Plafond\Listeners;

use Sanf\Core\Modules\Plafond\Jobs\SendEmailPlafondDisbursementSubmittedForClientJob;
use Sanf\Core\Modules\Plafond\Jobs\SendEmailPlafondDisbursementSubmittedForCustomerJob;

class SendEmailPlafondDisbursementSubmittedListener
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        $content = $event->content;

        $clientPayload = [
            'to' => $content->fullName,
            'Tanggal Pengajuan' => date_localized($content->createdAt, '%d %B %Y'),
            'Nama Perusahaan (Bowheer)' => $content->bowheer->name,
            'Nomor Pengajuan' => $content->disbursementNo,
            'Jumlah Invoice' => $content->invoiceCount,
            'Total Nilai Invoice' => 'Rp. ' . number_format($content->totalAmount, 0, ',', '.'),
            //'Nomor Surat' => $content->paymentAccDocumentNo,
            //'Tanggal Surat' => date_localized($content->paymentAccDocumentDate, '%d %B %Y'),
        ];

        dispatch(new SendEmailPlafondDisbursementSubmittedForClientJob($clientPayload, [$content->email->client]));

        if ($content->customerReview) {
            $customerPayload = [
                'to' => $content->bowheer->name,
                'url' => $content->webPartnerUrl,
                'Nama Client' => $content->fullName,
                'ID Pengajuan' => $content->disbursementNo,
                'Tanggal Pengajuan' => date_localized($content->createdAt, '%d %B %Y'),
                'Jumlah Invoice' => $content->invoiceCount,
                'Total Invoice' => 'Rp. ' . number_format($content->totalAmount, 0, ',', '.'),
                //'Nomor Surat' => $content->paymentAccDocumentNo,
                //'Tanggal Surat' => date_localized($content->paymentAccDocumentDate, '%d %B %Y'),
            ];

            dispatch(new SendEmailPlafondDisbursementSubmittedForCustomerJob($customerPayload, [$content->email->customer]));
        }
    }
}
