<?php

namespace Sanf\Core\Modules\Location;

use Spatie\DataTransferObject\DataTransferObject;

class GetListLocationDto extends DataTransferObject
{
    public ?string $keyword;

    public ?string $xid;

    public ?string $skip;

    public ?string $limit;

    public string $sort_by;

    public string $level;
}
