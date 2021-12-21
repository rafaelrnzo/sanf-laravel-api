<?php

namespace Sanf\Core\Modules\Insurance\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class ReadInsuranceClaimSubmissionByUserResponseDto extends CamelCaseDataTransferObject
{
    public int $id;
    public string $xid;
    public int $userId;
    public object $status;
    public \DateTimeImmutable $createdAt;
    public \DateTimeImmutable $updatedAt;
}
