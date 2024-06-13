<?php

namespace Sanf\Core\Modules\Plafond\Events;

use NbsPhp\Core\Event;

class PlafondDisbursementSubmittedNotificationEvent extends Event
{
    public $content;

    public function __construct($content)
    {
        $this->content = $content;
    }
}
