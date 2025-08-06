<?php

namespace Sanf\Core\Modules\Location;

use Spatie\DataTransferObject\DataTransferObject;

/**
 * @since CR2025
 */
class ListCityV2Dto extends DataTransferObject
{
    public int $skip = 0;

    public int $limit = 2147483647;

    public string $sort_by = 'name_asc';

    public ?string $order = null;

    public string $keyword = ''; // must be empty string to exclude non active items
}
