<?php

namespace Sanf\Core\Modules\Financing\Dto;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class ReadFinancingApplicationDto extends CamelCaseDataTransferObject
{
    public ?int $userId;
    public string $xid;
    public string $applicationXid;
}
