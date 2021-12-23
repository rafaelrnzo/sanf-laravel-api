<?php

namespace Sanf\Core\Modules\Invoice\Events;

use NbsPhp\Core\Event;

class InvoiceCollectionSubmissionAddedEvent extends Event
{
    public array $invoiceCollectionSubmissions;

    public function __construct($invoiceCollectionSubmissions)
    {
        $this->invoiceCollectionSubmissions = $invoiceCollectionSubmissions;
    }
}
