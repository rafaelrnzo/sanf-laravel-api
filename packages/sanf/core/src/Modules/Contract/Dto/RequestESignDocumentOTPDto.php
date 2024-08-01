<?php

namespace Sanf\Core\Modules\Contract\Dto;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class RequestESignDocumentOTPDto extends CamelCaseDataTransferObject
{
    public string $profileXid;
    public int $userId;
    public string $email;
    public string $msisdn;
    public string $referenceNo;
}
