<?php

namespace Sanf\Core\Modules\Installment\Responses;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class InstallmentEStatementReponse extends CamelCaseDataTransferObject
{
    /** @var mixed */
    public $fileName;

    /** @var mixed */
    public $fileType;

    /** @var mixed */
    public $url;
}
