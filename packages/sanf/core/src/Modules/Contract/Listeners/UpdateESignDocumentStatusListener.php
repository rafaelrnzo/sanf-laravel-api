<?php

namespace Sanf\Core\Modules\Contract\Listeners;

use Sanf\Core\Modules\Contract\Jobs\UpdateDocumentSignStatusJob;

class UpdateESignDocumentStatusListener
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event) {
        dispatch(new UpdateDocumentSignStatusJob($event->request));
    }
}
