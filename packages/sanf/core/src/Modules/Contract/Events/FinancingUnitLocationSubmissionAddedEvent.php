<?php

namespace Sanf\Core\Modules\Contract\Events;

use NbsPhp\Core\Event;

class FinancingUnitLocationSubmissionAddedEvent extends Event
{
    public $submission;

    public function __construct($submission)
    {
        $this->submission = $submission;
    }
}
