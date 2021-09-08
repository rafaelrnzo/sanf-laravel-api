<?php

namespace Sanf\Core\Modules\Astra;

use Spatie\DataTransferObject\DataTransferObject;

class GetListProductAstraDto extends DataTransferObject
{
    public ?string $skip;

    public ?string $limit;

    public string $sort_by;
}