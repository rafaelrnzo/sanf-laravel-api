<?php

namespace Sanf\Core\Modules\Installment\Responses;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;
use Sanf\Core\Traits\CastsNumericDtoProperties;

class InstallmentOutstandingResponse extends CamelCaseDataTransferObject
{
    use CastsNumericDtoProperties;

    public int $dueDate;
    public int $total;
    public int $principalLoan;
    public int $interestAmount;
    public int $penaltyFee;
}
