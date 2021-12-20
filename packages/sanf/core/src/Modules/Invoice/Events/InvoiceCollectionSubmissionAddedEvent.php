<?php

namespace Sanf\Core\Modules\Invoice\Events;

use NbsPhp\Core\Event;

class InvoiceCollectionSubmissionAddedEvent extends Event
{
    public $entity;

    public function __construct($entity)
    {
        $this->entity = $entity;
    }
}
