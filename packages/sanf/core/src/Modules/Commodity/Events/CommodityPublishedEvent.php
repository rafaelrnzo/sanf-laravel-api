<?php

namespace Sanf\Core\Modules\Commodity\Events;

class CommodityPublishedEvent extends AbstractCommodityEvent
{
    public $oldCommodity;

    public function __construct($newCommodity, $oldCommodity)
    {
        parent::__construct($newCommodity);
        $this->oldCommodity = $oldCommodity;
    }
}
