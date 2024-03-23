<?php

namespace Sanf\Core\Modules\Insurance\Events;

use NbsPhp\Core\Event;

class InsuranceClaimSubmissionAddedEvent extends Event
{
    public $insuranceClaimSubmission;

    /**
     * InsuranceClaimSubmissionAddedEvent constructor.
     * @param $insuranceClaimSubmission
     */
    public function __construct($insuranceClaimSubmission)
    {
        $this->insuranceClaimSubmission = $insuranceClaimSubmission;
    }
}
