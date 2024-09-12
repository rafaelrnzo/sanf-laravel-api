<?php

namespace Sanf\Core\Modules\Contract\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Sanf\Core\Modules\Contract\Jobs\UpdateDocumentSignStatusJob;

class CheckStatusESignDocumentListener implements ShouldQueue
{
    public $delay = 120;

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        dispatch(new UpdateDocumentSignStatusJob($event->request));
    }
}
