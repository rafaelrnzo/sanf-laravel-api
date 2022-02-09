<?php

namespace NbsPhp\Notification\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class ReadNotificationByUserResponseDto extends CamelCaseDataTransferObject
{
    public int $readCount;
}
