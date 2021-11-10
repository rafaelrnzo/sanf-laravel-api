<?php

namespace Sanf\Core\Modules\Financing\Events;

use NbsPhp\Core\Event;

class FinancingApplicationCreatedEvent extends Event
{
    public $financingApplication;

    public function __construct($financingApplication)
    {
        $this->financingApplication = $financingApplication;
    }
}
