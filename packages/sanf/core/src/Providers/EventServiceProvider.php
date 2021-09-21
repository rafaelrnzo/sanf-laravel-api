<?php

namespace Sanf\Core\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;
use Sanf\Core\Modules\Project\Events\ProjectCreatedEvent;
use Sanf\Core\Modules\Project\Listeners\SendEmailRequestApprovalProjectListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ProjectCreatedEvent::class => [
            //TODO LOGGING STATUS CHANGES USING EVENT
            SendEmailRequestApprovalProjectListener::class
        ]
    ];
}
