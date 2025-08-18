<?php

namespace Sanf\Core\Modules\PdcHold\Events;

use NbsPhp\Core\Event;

/**
 * @since CR2025
 */
class PdcHoldMultiContractSubmittedEvent extends Event
{
    public object $pdcHoldSubmission;
    public object $clientUser;

    public function __construct(object $pdcHoldSubmission, object $clientUser)
    {
        $this->pdcHoldSubmission = $pdcHoldSubmission;
        $this->clientUser = $clientUser;
    }
}
