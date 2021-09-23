<?php


namespace Sanf\Core\Modules\Commodity\Events;


class CommodityRejectedEvent extends AbstractCommodityEvent
{
    public $oldCommodity;

    public function __construct($newCommodity, $oldCommodity)
    {
        parent::__construct($newCommodity);
        $this->oldCommodity = $oldCommodity;
    }
}
