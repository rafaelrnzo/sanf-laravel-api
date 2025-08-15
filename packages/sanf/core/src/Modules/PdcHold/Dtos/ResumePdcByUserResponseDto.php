<?php

namespace Sanf\Core\Modules\PdcHold\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

/**
 * @since CR2025
 */
class ResumePdcByUserResponseDto extends CamelCaseDataTransferObject
{
    public string $xid;
    public array $giroXids;
}
