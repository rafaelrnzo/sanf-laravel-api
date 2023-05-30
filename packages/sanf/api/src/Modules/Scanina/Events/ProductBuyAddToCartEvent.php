<?php

namespace Sanf\Api\Modules\Scanina\Events;

use NbsPhp\Core\Event;

class ProductBuyAddToCartEvent extends Event
{
    public $request;
    public $profile;

    public function __construct($request, $profile)
    {
        $this->request = $request;
        $this->profile = $profile;
    }
}
