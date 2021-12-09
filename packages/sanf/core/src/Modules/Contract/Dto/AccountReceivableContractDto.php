<?php

namespace Sanf\Core\Modules\Contract\Dto;

use Spatie\DataTransferObject\DataTransferObject;

class AccountReceivableContractDto extends DataTransferObject
{

    public ?string $xid;

    public int $skip = 0;

    public int $limit = 10;

    public string $sort_by = 'earliest';

    public string $currency_type = 'IDR';
}