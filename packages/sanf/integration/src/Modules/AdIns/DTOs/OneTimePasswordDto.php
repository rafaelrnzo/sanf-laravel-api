<?php

namespace Sanf\Integration\Modules\AdIns\DTOs;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class OneTimePasswordDto extends CamelCaseDataTransferObject
{
    public string $msisdn;
    public ?string $email;
    public string $referenceNo;
}
