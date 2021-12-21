<?php

namespace Sanf\Core\Modules\Contract\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class ReadFinancingUnitLocationSubmissionByUserRequestDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public string $xid;
}
