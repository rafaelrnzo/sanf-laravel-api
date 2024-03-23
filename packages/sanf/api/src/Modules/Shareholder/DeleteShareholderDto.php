<?php

namespace Sanf\Api\Modules\Shareholder;

use Spatie\DataTransferObject\DataTransferObject;

class DeleteShareholderDto extends DataTransferObject
{
    public string $id;

    public string $no;
}
