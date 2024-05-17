<?php

namespace Sanf\Core\Modules\User\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

final class RegisterCoreAccountRequestDto extends CamelCaseDataTransferObject
{
    public string $fullName;
    public string $email;
    public string $handphone;
    public string $telephone;
}
