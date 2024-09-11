<?php

namespace Sanf\Core\Modules\Contract\Listeners;

use Sanf\Core\Modules\Contract\Jobs\UpdateDocumentSignStatusByCallbackJob;

class UpdateESignDocumentStatusByCallbackListener
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        dispatch(new UpdateDocumentSignStatusByCallbackJob($event->request));
    }
}
