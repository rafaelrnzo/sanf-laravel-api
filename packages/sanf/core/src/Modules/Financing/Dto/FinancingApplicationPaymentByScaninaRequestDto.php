<?php

namespace Sanf\Core\Modules\Financing\Dto;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class FinancingApplicationPaymentByScaninaRequestDto extends CamelCaseDataTransferObject
{
    public float $amount = 0;
    public float $downPaymentPercentage = 0;
    public float $downPaymentAmount = 0;
    public float $firstPaymentAmount = 0;
    public float $taxAmount = 0;
    public float $vatAmount = 0;
    public float $backhargeAmount = 0;
    public float $otherAmount = 0;
    public float $totalAmount = 0;
    public int $tenor = 0;
}
