<?php

namespace Sanf\Core\Modules\PdcHold\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

/**
 * @since CR2025
 */
class BrowsePdcHoldSubmissionByUserRequestDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public string $profileXid;
    public ?int $statusId;
    public ?int $type;
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
}
