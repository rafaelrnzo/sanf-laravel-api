<?php

namespace Sanf\Core\Modules\Contract\Dto;

use Spatie\DataTransferObject\DataTransferObject;

class BrowseProvinceDto extends DataTransferObject
{
    public int $user_id;
    public ?string $keyword;
    public ?int $skip;
    public ?int $limit;
    public ?string $sort_by;
}
