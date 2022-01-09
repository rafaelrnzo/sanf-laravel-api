<?php

namespace Sanf\Core\Modules\Notification\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class AddPushNotificationByExternalResponseDto extends CamelCaseDataTransferObject
{
    public int $id;
    public string $xid;
    public ?\DateTimeImmutable $createdAt;
    public ?\DateTimeImmutable $updatedAt;
}
