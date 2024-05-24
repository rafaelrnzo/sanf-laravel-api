<?php

namespace Sanf\Core\Modules\Plafond\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class ReadPlafondDisbursementRequestDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public string $profileXid;
    public string $plafondXid;
    public string $disbursementXid;
    public int $limit = 1;
}
