<?php

namespace Sanf\Integration\Modules\AdIns\DTOs;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class DocumentDto extends CamelCaseDataTransferObject
{
    public ?array $documentsId;
    public ?string $documentId;
    public ?string $referenceNo;
    public ?string $email;
    public ?string $msisdn;
    public ?string $password;
    public ?string $ip;
    public ?string $browser;
    public ?string $otp;
    public ?string $selfPhoto;
}
