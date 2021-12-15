<?php

namespace Sanf\Core\Modules\Prepayment\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class AddPrepaymentSubmissionByUserRequestDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public string $profileXid;
    public object $prepaymentSimulation;
}
