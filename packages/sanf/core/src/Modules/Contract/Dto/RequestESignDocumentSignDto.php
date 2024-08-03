<?php

namespace Sanf\Core\Modules\Contract\Dto;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class RequestESignDocumentSignDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public string $sanfId;
    public string $email;
    public string $msisdn;
    public string $documentId;
    public ?string $password;
    public string $otp;
    public string $ipAddress;
    public string $userAgent;
}
