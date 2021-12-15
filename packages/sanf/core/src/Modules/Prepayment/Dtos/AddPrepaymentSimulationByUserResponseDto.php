<?php

namespace Sanf\Core\Modules\Prepayment\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class AddPrepaymentSimulationByUserResponseDto extends CamelCaseDataTransferObject
{
    public string $contractNo;
    public ?\DateTimeImmutable $prepaymentDate;
    public string $totalPrepayment;
    public string $currencyType;
    public array $items;
}
