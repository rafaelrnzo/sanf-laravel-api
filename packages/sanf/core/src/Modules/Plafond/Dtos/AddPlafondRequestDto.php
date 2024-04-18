<?php

namespace Sanf\Core\Modules\Plafond\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class AddPlafondRequestDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public $profile;
    public string $profileXid;
    public string $typeId;
    public string $amount;
    public array $notes;
}
