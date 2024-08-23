<?php

namespace Sanf\Core\Modules\Contract\Events;

use NbsPhp\Core\Event;

class AdInsDocumentSignCallbackEvent extends Event
{
    public object $request;

    public function __construct(object $request)
    {
        $this->request = $request;
    }
}
