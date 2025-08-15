<?php

namespace Sanf\Core\Modules\PdcHold\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

/**
 * @since CR2025
 */
class AddPdcHoldMultiContractByUserRequestDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public string $profileXid;
    public ?\DateTimeImmutable $period;
    public array $multiContract;
    public PdcHoldReasonRequestDto $reason;
}
