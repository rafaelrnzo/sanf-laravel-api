<?php

namespace Sanf\Core\Modules\PdcHold\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

/**
 * @since CR2025
 */
class PdcHoldMultiContractRequestDto extends CamelCaseDataTransferObject
{
    public string $pdcNo;
    public string $amount;
    public string $currencyType;
    public string $date;
    public ?string $pdcType;
    public string $contractNo;
}
