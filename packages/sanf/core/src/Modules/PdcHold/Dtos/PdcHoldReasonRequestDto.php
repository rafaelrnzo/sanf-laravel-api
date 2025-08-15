<?php

namespace Sanf\Core\Modules\PdcHold\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

/**
 * @since CR2025
 */
class PdcHoldReasonRequestDto extends CamelCaseDataTransferObject
{
    public int $id;
    public string $value;
}
