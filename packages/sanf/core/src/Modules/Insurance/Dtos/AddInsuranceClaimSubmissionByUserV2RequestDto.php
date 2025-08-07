<?php

namespace Sanf\Core\Modules\Insurance\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

/**
 * @since CR2025
 */
class AddInsuranceClaimSubmissionByUserV2RequestDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public string $profileXid;
    public string $contractNo;
    public \DateTimeImmutable $incidentDate;
    public array $imageFiles = [];
    public string $description;
    public array $locationMetadata;
    public FinancingUnitV2RequestDto $financingUnit;
    public string $picName;
    public string $picPhoneNumber;
}
