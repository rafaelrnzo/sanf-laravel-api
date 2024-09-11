<?php

namespace Sanf\Core\Modules\Contract\Events;

use NbsPhp\Core\Event;

class AdInsRegisterActivationEvent extends Event
{
    public object $request;

    public function __construct(object $request)
    {
        $this->request = $request;
    }
}
