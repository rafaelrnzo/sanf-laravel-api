<?php

namespace Sanf\Core\Modules\Contract\Listeners;

use Sanf\Core\Modules\Contract\Jobs\UpdateAdInsUserStatusByCallbackJob;

class UpdateAdInsUserStatusByCallbackListener
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        dispatch(new UpdateAdInsUserStatusByCallbackJob($event->request));
    }
}
