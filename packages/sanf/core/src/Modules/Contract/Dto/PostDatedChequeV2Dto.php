<?php

namespace Sanf\Core\Modules\Contract\Dto;

use Spatie\DataTransferObject\DataTransferObject;

/**
 * From SANF Core.
 *
 * @since CR2025
 */
class PostDatedChequeV2Dto extends DataTransferObject
{
    public ?string $user_id;

    public string $profile_xid;

    public ?array $contract_no;

    public ?int $status_id;

    public ?\DateTimeImmutable $date_start;

    public ?\DateTimeImmutable $date_end;

    public int $skip = 0;

    public int $limit = 10;

    public string $sort_by = 'earliest';
}
