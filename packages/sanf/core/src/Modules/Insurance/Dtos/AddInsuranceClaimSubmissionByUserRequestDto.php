<?php

namespace Sanf\Core\Modules\Insurance\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class AddInsuranceClaimSubmissionByUserRequestDto extends CamelCaseDataTransferObject
{
//    public \DateTimeImmutable $dateTime;
    public int $userId;
    public string $profileXid;
//    public int $amount;
//    public float $total;
//    public string $title;
//    public ?string $description;
//    public bool $isEnabled;
}
