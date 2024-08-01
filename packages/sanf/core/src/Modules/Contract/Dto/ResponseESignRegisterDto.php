<?php

namespace Sanf\Core\Modules\Contract\Dto;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class ResponseESignRegisterDto extends CamelCaseDataTransferObject
{
    public string $msisdn;
    public string $email;
    public string $nik;
    public string $transactionNo;
}
