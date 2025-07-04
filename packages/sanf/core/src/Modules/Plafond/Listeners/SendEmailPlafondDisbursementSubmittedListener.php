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

        $customerPayload = [
            'fullName' => $content->fullName,
            'bowheer' => $content->bowheer,
            'company_info' => $content->company_info,
            'invoices' => $content->invoices,
            'clientTargetBank' => $content->clientTargetBank,
            'allocationsTargetBank' => $content->allocationsTargetBank,
            'plafond_id' => $content->plafond_id,
            'payment_acc_document' => $content->payment_acc_document ?? null,
            'invoice_documents' => $content->invoice_documents ?? [],
            'invoice_photos' => $content->invoice_photos ?? [],
            'other_documents' => $content->other_documents ?? [],
            //'Nomor Surat' => $content->paymentAccDocumentNo,
            //'Tanggal Surat' => date_localized($content->paymentAccDocumentDate, '%d %B %Y'),
        ];

        dispatch(new SendEmailPlafondDisbursementSubmittedForCustomerJob(
            $customerPayload,
            [$content->email->customer],
            $content->customerReview
        ));
    }
}
