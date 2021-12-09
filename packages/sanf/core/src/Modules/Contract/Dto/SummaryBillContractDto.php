<?php

namespace Sanf\Core\Modules\Contract\Dto;

use Spatie\DataTransferObject\DataTransferObject;

class SummaryBillContractDto extends DataTransferObject
{
    public ?string $xid;

    public string $contract_no;

    public int $skip = 0;

    public int $limit = 10;

    public string $sort_by = 'earliest';
}