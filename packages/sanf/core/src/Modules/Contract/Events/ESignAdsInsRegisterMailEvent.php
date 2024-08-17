<?php

namespace Sanf\Core\Modules\Contract\Events;

use NbsPhp\Core\Event;

class ESignAdsInsRegisterMailEvent extends Event
{
    public object $recipient;

    public function __construct(object $recipient)
    {
        $this->recipient = $recipient;
    }
}
