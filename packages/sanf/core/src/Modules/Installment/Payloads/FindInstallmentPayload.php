<?php

namespace Sanf\Core\Modules\Installment\Payloads;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class FindInstallmentPayload extends CamelCaseDataTransferObject
{
    /** @var mixed */
    public $contractNo;

    /** @var mixed */
    public $dueDate;

    /** @var mixed */
    public $userId;

    /** @var string */
    public $profileXid;
}
