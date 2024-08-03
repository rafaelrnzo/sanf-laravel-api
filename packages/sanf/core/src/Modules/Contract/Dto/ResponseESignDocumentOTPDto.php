<?php

namespace Sanf\Core\Modules\Contract\Dto;

use Carbon\CarbonImmutable;
use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class ResponseESignDocumentOTPDto extends CamelCaseDataTransferObject
{
    public string $msisdn;
    public string $email;
    public CarbonImmutable $expiredAt;
    public string $referenceNo;
    public string $transactionNo;
}
