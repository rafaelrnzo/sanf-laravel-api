<?php

namespace Sanf\Core\Modules\Installment\Responses;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class InstallmentContractResponse extends CamelCaseDataTransferObject
{
    /** @var mixed */
    public $contractNo;

    /** @var mixed */
    public $contractDate;

    /** @var mixed */
    public $supplierName;

    /** @var mixed */
    public $financingTypeId;

    /** @var mixed */
    public $financingTypeDesc;

    /** @var mixed */
    public $financingFacilityId;

    /** @var mixed */
    public $financingFacilityDesc;

    /** @var mixed */
    public $financingMethodId;

    /** @var mixed */
    public $financingMethodDesc;

    /** @var mixed */
    public $financingAmount;

    /** @var mixed */
    public $tenorValue;

    /** @var mixed */
    public $tenorUnit;

    /** @var mixed */
    public $statusId;

    /** @var mixed */
    public $statusDesc;

    /** @var mixed */
    public $dueDate;

    /** @var mixed */
    public $completedDate;

    /** @var mixed */
    public $interestRate;

    /** @var mixed */
    public $plafondType;

    /** @var mixed */
    public $downPayment;

    /** @var mixed */
    public $paidAmount;

    /** @var mixed */
    public $outstandingAmount;
}
