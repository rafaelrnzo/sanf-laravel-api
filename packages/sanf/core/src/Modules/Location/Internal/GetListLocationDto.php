<?php

namespace Sanf\Core\Modules\Location\Internal;


use Spatie\DataTransferObject\DataTransferObject;

class GetListLocationDto extends DataTransferObject
{
    public ?string $keyword;

    public ?string $xid;

    public ?string $parent_xid;

    public ?string $skip;

    public ?string $limit;

    public string $sort_by;

    public int $adm_area_id;
}