<?php

namespace Sanf\Core\Modules\Insurance\Events;

use NbsPhp\Core\Event;

class InsuranceClaimSubmissionAddedEvent extends Event
{
    public $entity;

    /**
     * InsuranceClaimSubmissionAddedEvent constructor.
     * @param $entity
     */
    public function __construct($entity)
    {
        $this->entity = $entity;
    }

}
