<?php

namespace Sanf\Api\Modules\User\Dto;

use Spatie\DataTransferObject\DataTransferObject;

class GetListTitleDto extends DataTransferObject
{
    public string $type;
}