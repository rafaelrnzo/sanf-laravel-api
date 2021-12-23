<?php

namespace Sanf\Core\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;
use Sanf\Core\Modules\Commodity\Events\CommodityCreatedEvent;
use Sanf\Core\Modules\Commodity\Events\CommodityUpdatedEvent;
use Sanf\Core\Modules\Commodity\Listeners\SendEmailRequestApprovalCommodityListener;
use Sanf\Core\Modules\Contract\Events\FinancingUnitLocationSubmissionAddedEvent;
use Sanf\Core\Modules\Contract\Listeners\SendEmailRequestChangeFinancingUnitLocationListener;
use Sanf\Core\Modules\Financing\Events\FinancingApplicationCreatedEvent;
use Sanf\Core\Modules\Financing\Listeners\SendEmailNewFinancingApplicationListener;
use Sanf\Core\Modules\Invoice\Events\InvoiceCollectionSubmissionAddedEvent;
use Sanf\Core\Modules\Invoice\Listeners\SendEmailNewInvoiceCollectionSubmissionListener;
use Sanf\Core\Modules\Plafond\Events\PlafondIncreaseRequestedEvent;
use Sanf\Core\Modules\Plafond\Events\PlafondRequestedEvent;
use Sanf\Core\Modules\Plafond\Listeners\SendEmailRequestIncreasePlafondListener;
use Sanf\Core\Modules\Plafond\Listeners\SendEmailRequestNewPlafondListener;
use Sanf\Core\Modules\Prepayment\Events\PrepaymentSubmissionAddedEvent;
use Sanf\Core\Modules\Prepayment\Listeners\SendEmailNewPrepaymentSubmissionListener;
use Sanf\Core\Modules\Project\Events\ProjectCreatedEvent;
use Sanf\Core\Modules\Project\Events\ProjectUpdatedEvent;
use Sanf\Core\Modules\Project\Listeners\SendEmailRequestApprovalProjectListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ProjectCreatedEvent::class => [
            SendEmailRequestApprovalProjectListener::class
        ],
        CommodityCreatedEvent::class => [
            SendEmailRequestApprovalCommodityListener::class
        ],
        ProjectUpdatedEvent::class => [
            SendEmailRequestApprovalProjectListener::class
        ],
        CommodityUpdatedEvent::class => [
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
        PrepaymentSubmissionAddedEvent::class => [
            SendEmailNewPrepaymentSubmissionListener::class
        ],
        FinancingUnitLocationSubmissionAddedEvent::class => [
            SendEmailRequestChangeFinancingUnitLocationListener::class
        ],
        InvoiceCollectionSubmissionAddedEvent::class => [
            SendEmailNewInvoiceCollectionSubmissionListener::class
        ]
    ];
}
