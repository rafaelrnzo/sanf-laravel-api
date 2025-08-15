<?php

namespace Sanf\Core\Modules\PdcHold\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

/**
 * @since CR2025
 */
class ReadPdcHoldSubmissionByUserResponseDto extends CamelCaseDataTransferObject
{
    public int $id;
    public string $xid;
    public object $status;
    public object $type;
    public \DateTimeImmutable $dateStart;
    public ?\DateTimeImmutable $dateEnd;
    public array $giros;
    public \DateTimeImmutable $createdAt;
    public \DateTimeImmutable $updatedAt;
}
