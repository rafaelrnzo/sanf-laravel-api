<?php

namespace Sanf\Dashboard\Modules\User\Events;

use NbsPhp\Core\Event;

class AccountBindingCreatedByCoreNotificationEvent extends Event
{
    public $content;

    public function __construct($content)
    {
        $this->content = $content;
    }
}
