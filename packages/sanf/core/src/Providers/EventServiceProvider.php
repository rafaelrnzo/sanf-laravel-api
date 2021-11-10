<?php

namespace Sanf\Core\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;
use Sanf\Core\Modules\Commodity\Events\CommodityCreatedEvent;
use Sanf\Core\Modules\Commodity\Events\CommodityUpdatedEvent;
use Sanf\Core\Modules\Commodity\Listeners\SendEmailRequestApprovalCommodityListener;
use Sanf\Core\Modules\Financing\Events\FinancingApplicationCreatedEvent;
use Sanf\Core\Modules\Financing\Listeners\SendEmailNewFinancingApplicationListener;
use Sanf\Core\Modules\Plafond\Events\PlafondIncreaseRequestedEvent;
use Sanf\Core\Modules\Plafond\Events\PlafondRequestedEvent;
use Sanf\Core\Modules\Plafond\Listeners\SendEmailRequestIncreasePlafondListener;
use Sanf\Core\Modules\Plafond\Listeners\SendEmailRequestNewPlafondListener;
use Sanf\Core\Modules\Project\Events\ProjectCreatedEvent;
use Sanf\Core\Modules\Project\Events\ProjectUpdatedEvent;
use Sanf\Core\Modules\Project\Listeners\SendEmailRequestApprovalProjectListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ProjectCreatedEvent::class => [
            //TODO LOGGING STATUS CHANGES USING EVENT
            SendEmailRequestApprovalProjectListener::class
        ],
        CommodityCreatedEvent::class => [
            //TODO LOGGING STATUS CHANGES USING EVENT
            SendEmailRequestApprovalCommodityListener::class
        ],
        ProjectUpdatedEvent::class => [
            //TODO LOGGING STATUS CHANGES USING EVENT
            SendEmailRequestApprovalProjectListener::class
        ],
        CommodityUpdatedEvent::class => [
            //TODO LOGGING STATUS CHANGES USING EVENT
            SendEmailRequestApprovalCommodityListener::class
        ],
        PlafondRequestedEvent::class => [
            SendEmailRequestNewPlafondListener::class
        ],
        PlafondIncreaseRequestedEvent::class => [
            SendEmailRequestIncreasePlafondListener::class
        ],
        FinancingApplicationCreatedEvent::class => [
            SendEmailNewFinancingApplicationListener::class
        ],
    ];
}
