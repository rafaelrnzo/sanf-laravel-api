<?php

namespace NbsPhp\Notification\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;
use NbsPhp\Core\Dto\PaginateResponseDto;

class BrowseNotificationByUserResponseDto extends CamelCaseDataTransferObject
{
    public array $data;
    public PaginateResponseDto $paginate;
}
