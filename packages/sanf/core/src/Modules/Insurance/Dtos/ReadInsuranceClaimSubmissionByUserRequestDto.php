<?php

namespace Sanf\Core\Modules\Insurance\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class ReadInsuranceClaimSubmissionByUserRequestDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public string $xid;
}
