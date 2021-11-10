<?php

namespace Sanf\Core\Modules\Plafond\Events;

use NbsPhp\Core\Event;

class PlafondRequestedEvent extends Event
{
    public $plafondRequest;
    public $profile;

    public function __construct($plafondRequest, $profile)
    {
        $this->plafondRequest = $plafondRequest;
        $this->profile = $profile;
    }
}
