<?php

namespace Sanf\Core\Modules\Promo;

use Spatie\DataTransferObject\DataTransferObject;

class GetListPromoDto extends DataTransferObject
{
    public ?string $skip;

    public ?string $limit;

    public string $sort_by;
}