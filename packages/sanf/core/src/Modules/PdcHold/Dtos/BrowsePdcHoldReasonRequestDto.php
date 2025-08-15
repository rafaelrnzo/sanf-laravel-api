<?php

namespace Sanf\Core\Modules\PdcHold\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

/**
 * @since CR2025
 */
class BrowsePdcHoldReasonRequestDto extends DataTransferObject
{
    public ?int $skip;
    public ?int $limit;
}
