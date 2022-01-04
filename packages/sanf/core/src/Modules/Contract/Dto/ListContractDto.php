<?php

namespace Sanf\Core\Modules\Contract\Dto;

use Spatie\DataTransferObject\DataTransferObject;

class ListContractDto extends DataTransferObject
{
    public ?string $user_id;

    public string $profile_xid;

    public ?string $contract_type = 'active';

    public int $skip = 0;

    public int $limit = 10;

    public string $sort_by = 'earliest';
}
