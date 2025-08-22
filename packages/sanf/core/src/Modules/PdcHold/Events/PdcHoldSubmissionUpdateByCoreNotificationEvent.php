<?php

namespace Sanf\Core\Modules\PdcHold\Events;

use NbsPhp\Core\Event;

/**
 * @since CR2025
 */
class PdcHoldSubmissionUpdateByCoreNotificationEvent extends Event
{
    public $content;

    public function __construct($content)
    {
        $this->content = $content;
    }
}
