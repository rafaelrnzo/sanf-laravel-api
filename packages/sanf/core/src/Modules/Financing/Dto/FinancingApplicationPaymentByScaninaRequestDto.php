<?php

namespace Sanf\Core\Modules\Financing\Dto;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class FinancingApplicationPaymentByScaninaRequestDto extends CamelCaseDataTransferObject
{
    public float $amount;
    public float $downPaymentPercentage;
    public float $downPaymentAmount;
    public float $firstPaymentAmount;
    public float $totalAmount;
    public int $tenor;
}
