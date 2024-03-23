<?php

namespace Sanf\Core\Modules\Location;

use Spatie\DataTransferObject\DataTransferObject;

class ListCityDto extends DataTransferObject
{
    public int $skip = 0;

    public int $limit = 10;

    public string $sort_by = 'name_asc';
}
