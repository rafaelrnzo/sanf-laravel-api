<?php

namespace Sanf\Core\Modules\Prepayment\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class AddPrepaymentSubmissionByUserResponseDto extends CamelCaseDataTransferObject
{
    public string $xid;
    public int $statusId;
    public int $userId;
    public string $profileXid;
    public string $contractNo;
    public $prepaymentDate;
    public $totalPrepayment;
    public string $currencyType;
    public array $items;
}
