<?php

namespace Sanf\Core\Modules\Contract\Dto;

use Spatie\DataTransferObject\DataTransferObject;

class ContractPostDatedChequeDto extends DataTransferObject
{
    public ?string $user_id;

    public ?string $profile_xid;

    public int $skip = 0;

    public int $limit = 10;

    public string $sort_by = 'earliest';

    public ?string $keyword;

    public ?\DateTimeImmutable $date_start; // @since CR2025.

    public ?\DateTimeImmutable $date_end; // @since CR2025.
}
