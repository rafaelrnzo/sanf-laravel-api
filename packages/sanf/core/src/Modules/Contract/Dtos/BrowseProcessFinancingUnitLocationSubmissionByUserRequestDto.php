<?php

namespace Sanf\Core\Modules\Contract\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class BrowseProcessFinancingUnitLocationSubmissionByUserRequestDto extends CamelCaseDataTransferObject
{
    public int $userId;

    public string $profileXid;

    public string $xid;

    public int $skip = 0;

    public int $limit = 10;

    public string $sortBy = 'earliest';
}
