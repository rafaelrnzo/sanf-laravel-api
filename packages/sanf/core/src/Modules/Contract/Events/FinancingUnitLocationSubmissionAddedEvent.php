<?php

namespace Sanf\Core\Modules\Contract\Events;

use NbsPhp\Core\Event;

class FinancingUnitLocationSubmissionAddedEvent extends Event
{
    //TODO HERE
    public $entity;

    public function __construct($entity)
    {
        $this->entity = $entity;
    }
}
