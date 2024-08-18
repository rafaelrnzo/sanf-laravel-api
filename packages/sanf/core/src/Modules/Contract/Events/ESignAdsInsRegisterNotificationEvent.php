<?php

namespace Sanf\Core\Modules\Contract\Events;

use NbsPhp\Core\Event;

class ESignAdsInsRegisterNotificationEvent extends Event
{
    public string $userId;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }
}
