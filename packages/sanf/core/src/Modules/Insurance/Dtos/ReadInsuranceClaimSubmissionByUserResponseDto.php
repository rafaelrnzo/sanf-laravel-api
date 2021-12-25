<?php

namespace Sanf\Core\Modules\Insurance\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class ReadInsuranceClaimSubmissionByUserResponseDto extends CamelCaseDataTransferObject
{
    public int $id;
    public string $xid;
    public int $userId;
    public object $status;
    public string $serialNo;
    public string $polisNo;
    public string $brandTypeModel;
    public string $year;
    public $locationMetadata;
    public \DateTimeImmutable $incidentDate;
    public string $description;
    public array $imageFiles;
    public \DateTimeImmutable $createdAt;
    public \DateTimeImmutable $updatedAt;
}
