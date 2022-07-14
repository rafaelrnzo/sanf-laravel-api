<?php

namespace Sanf\Core\Modules\Contract\Events;

use NbsPhp\Core\Event;

class ESignDocumentDownloadEvent extends Event
{
    public string $email;
    public object $document;

    public function __construct(string $email, object $document)
    {
        $this->email = $email;
        $this->document = $document;
    }
}
