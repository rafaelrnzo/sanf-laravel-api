<?php

namespace Sanf\Core\Modules\User\Dtos;

use Carbon\Carbon;
use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class PostDeactivateAccountDto extends CamelCaseDataTransferObject
{
    public string $xid;
    public int $userId;
    public string $email;
    public string $personalXid;
    public int $statusId;
    public Carbon $restoreExpiredAt;
    public object $createdBy;
}
