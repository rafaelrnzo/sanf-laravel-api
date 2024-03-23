<?php

namespace Sanf\Core\Modules\Commodity\Events;

class CommodityApprovedEvent extends AbstractCommodityEvent
{
    public $oldCommodity;
    public $user;

    public function __construct($newCommodity, $oldCommodity, $user)
    {
        parent::__construct($newCommodity);
        $this->oldCommodity = $oldCommodity;
        $this->user = $user;
    }
}
