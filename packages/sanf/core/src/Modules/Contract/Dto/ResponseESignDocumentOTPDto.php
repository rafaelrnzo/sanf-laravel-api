<?php

namespace Sanf\Core\Modules\Contract\Dto;

use DateTime;
use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class ResponseESignDocumentOTPDto extends CamelCaseDataTransferObject
{
    public string $msisdn;
    public string $email;
    public DateTime $expiredAt;
    public string $referenceNo;
    public string $transactionNo;
}
