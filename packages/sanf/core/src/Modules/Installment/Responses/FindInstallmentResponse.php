<?php

namespace Sanf\Core\Modules\Installment\Responses;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class FindInstallmentResponse extends CamelCaseDataTransferObject
{
    /** @var mixed */
    public $totalAmount;

    /** @var mixed */
    public $subtotalInstallment;

    /** @var mixed */
    public $dueDate;

    /** @var mixed */
    public $penaltyFee;

    /** @var mixed */
    public $principalLoan;

    /** @var mixed */
    public $interestAmount;

    /** @var mixed */
    public $status;

    /** @var mixed */
    public $sequenceNo;

    /** @var mixed */
    public $sequenceTotal;

    /** @var mixed */
    public $paymentXid;

    /** @var \Sanf\Core\Modules\Installment\Responses\InstallmentContractResponse */
    public $contract;

    /** @var \Sanf\Core\Modules\Installment\Responses\InstallmentEStatementReponse|null */
    public $eStatementFile;

    /** @var array|\Sanf\Core\Modules\Installment\Responses\InstallmentOutstandingResponse[] */
    public $outstandingInstallments;
}
