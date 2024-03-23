<?php

namespace Sanf\Core\Modules\Branch;

use Spatie\DataTransferObject\DataTransferObject;

class ListBranchRequestDto extends DataTransferObject
{
    public int $limit = 10;

    public int $offset = 0;
}
