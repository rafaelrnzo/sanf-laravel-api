<?php

namespace NbsPhp\Notification\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class ReadNotificationByUserRequestDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public ?array $xids;
}
