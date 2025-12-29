<?php

namespace Sanf\Core\Modules\Installment\Responses;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;
use Sanf\Core\Traits\CastsNumericDtoProperties;

class InstallmentOutstandingResponse extends CamelCaseDataTransferObject
{
    use CastsNumericDtoProperties;

    public int $dueDate;
    public float $total;
    public float $pricipalLoan;
    public float $interestAmount;
    public float $penaltyFee;
}
