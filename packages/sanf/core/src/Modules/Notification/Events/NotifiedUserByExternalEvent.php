<?php

namespace Sanf\Core\Modules\Notification\Events;

use NbsPhp\Core\Event;

class NotifiedUserByExternalEvent extends Event
{

    public array $contents;

    public function __construct(array $contents)
    {
        $this->contents = $contents;
    }
}
