<?php

namespace Sanf\Core\Modules\Contract\Listeners;

use Illuminate\Support\Str;
use Sanf\Core\Modules\Contract\Jobs\SendEmailDownloadESignDocumentJob;
use Sanf\Core\Modules\Contract\Jobs\SendEmailFinancingUnitLocationJob;

class SendEmailDownloadESignDocumentListener
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        $data = [
            'fullName' => $event->document->full_name,
            'documentName' => $event->document->document_name ?? $event->document->document_id . ".pdf",
            'path' => $event->document->document_file->path,
        ];

        dispatch(new SendEmailDownloadESignDocumentJob($event->email, $data));
    }
}
