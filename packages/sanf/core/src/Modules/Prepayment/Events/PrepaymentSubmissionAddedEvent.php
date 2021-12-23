<?php

namespace Sanf\Core\Modules\Prepayment\Events;

use NbsPhp\Core\Event;

class PrepaymentSubmissionAddedEvent extends Event
{
    public $prepaymentSubmission;

    public function __construct($prepaymentSubmission)
    {
        $this->prepaymentSubmission = $prepaymentSubmission;
    }
}
