<?php

namespace Sanf\Core\Modules\PdcHold\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

/**
 * @since CR2025
 */
class ResumePdcByUserRequestDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public string $profileXid;
    public array $giroXids;
}
