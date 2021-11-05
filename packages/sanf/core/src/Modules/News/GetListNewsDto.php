<?php

namespace Sanf\Core\Modules\News;

use Spatie\DataTransferObject\DataTransferObject;

class GetListNewsDto extends DataTransferObject
{
    public ?string $keyword;

    public ?string $skip;

    public ?string $limit;

    public string $sort_by;

    public ?int $timestamp;
}
