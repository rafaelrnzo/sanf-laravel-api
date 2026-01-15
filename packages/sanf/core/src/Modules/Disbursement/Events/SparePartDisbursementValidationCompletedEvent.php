<?php

namespace Sanf\Core\Modules\Disbursement\Events;

use NbsPhp\Core\Event;

class SparePartDisbursementValidationCompletedEvent extends Event
{
    public $bowheerId;
    public $batchNumber;

    public function __construct(string $bowheerId, string $batchNumber)
    {
        $this->bowheerId = $bowheerId;
        $this->batchNumber = $batchNumber;
    }
}
