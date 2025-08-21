<?php

namespace Sanf\Core\Modules\PdcHold\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

/**
 * @since CR2025
 */
class UpdateStatusPdcSubmissionByCoreRequestDto extends CamelCaseDataTransferObject
{
    public string $custId;
    public string $pdcHoldXid;
    public string $status;
}
