<?php

namespace Sanf\Core\Modules\Contract\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class ReadFinancingUnitLocationSubmissionByUserResponseDto extends CamelCaseDataTransferObject
{
    public int $id;
    public string $xid;
    public int $userId;
    public \DateTimeImmutable $createdAt;
    public \DateTimeImmutable $updatedAt;
}
