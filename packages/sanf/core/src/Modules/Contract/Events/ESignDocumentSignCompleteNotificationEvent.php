<?php

namespace Sanf\Core\Modules\Contract\Events;

use NbsPhp\Core\Event;

class ESignDocumentSignCompleteNotificationEvent extends Event
{
    public string $userId;
    public string $documentName;

    public function __construct(string $userId, string $documentName)
    {
        $this->userId = $userId;
        $this->documentName = $documentName;
    }
}
