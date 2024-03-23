<?php

namespace Sanf\Core\Modules\Commodity;

use MyCLabs\Enum\Enum;

/**
 * Class CommodityStatus.
 */
class CommodityStatus extends Enum
{
    const WAITING_APPROVAL = 10;
    const REJECTED = 20;
    const PUBLISHED = 30;
    const UNPUBLISHED = 40;
}
