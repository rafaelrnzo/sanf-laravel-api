<?php

namespace Sanf\Core\Modules\Installment\Responses;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class FindInstallmentResponse extends CamelCaseDataTransferObject
{
    /** @var mixed */
    public $totalAmount;

    /** @var mixed */
    public $dueDate;

    /** @var mixed */
    public $penaltyFee;

    /** @var mixed */
    public $principalLoan;

    /** @var mixed */
    public $interestAmount;

    /** @var mixed */
    public $downPayment;

    /** @var mixed */
    public $paidDownPayment;

    /** @var mixed */
    public $paidAmount;

    /** @var mixed */
    public $status;

    /** @var InstallmentContractResponse|mixed */
    public $contract;

    /** @var InstallmentEStatementReponse|mixed */
    public $eStatementFile;
}
