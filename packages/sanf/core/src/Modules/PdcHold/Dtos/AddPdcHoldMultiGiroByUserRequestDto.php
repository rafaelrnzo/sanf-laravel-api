<?php

namespace Sanf\Core\Modules\PdcHold\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

/**
 * @since CR2025
 */
class AddPdcHoldMultiGiroByUserRequestDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public string $profileXid;
    public ?\DateTimeImmutable $dateStart;
    public ?\DateTimeImmutable $dateEnd;
    public string $contractNo;
    public array $multiGiro;
    public PdcHoldReasonRequestDto $reason;
}
