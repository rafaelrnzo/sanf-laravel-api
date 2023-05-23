<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

class PostUserRegisterRequestDto extends ScaninaRequestDataTransferObject
{
    public int $userId;
    public string $xid;
    public string $fullName;
    public string $msisdn;
    public string $cityId;
    public string $password;
}
