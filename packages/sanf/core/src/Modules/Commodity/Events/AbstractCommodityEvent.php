<?php

namespace Sanf\Core\Modules\Commodity\Events;

use NbsPhp\Core\Event;

abstract class AbstractCommodityEvent extends Event
{
    public $commodity;

    public function __construct($commodity)
    {
        $this->commodity = $commodity;
    }
}
