<?php

namespace NbsPhp\Notification\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class BrowseNotificationByUserRequestDto extends CamelCaseDataTransferObject
{
    public ?int $userId;
    public ?int $timestamp;
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
    public ?string $keyword;
}
