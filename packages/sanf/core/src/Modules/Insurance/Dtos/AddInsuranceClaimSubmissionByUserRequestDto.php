<?php

namespace Sanf\Core\Modules\Insurance\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class AddInsuranceClaimSubmissionByUserRequestDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public string $profileXid;
    public string $contractNo;
    public \DateTimeImmutable $incidentDate;
    public array $imageFiles;
    public string $description;
    public array $locationMetadata;
    public FinancingUnitRequestDto $financingUnit;
}
