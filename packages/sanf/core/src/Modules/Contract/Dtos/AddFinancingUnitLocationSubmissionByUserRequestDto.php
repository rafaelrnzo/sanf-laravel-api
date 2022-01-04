<?php

namespace Sanf\Core\Modules\Contract\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class AddFinancingUnitLocationSubmissionByUserRequestDto extends CamelCaseDataTransferObject
{
    public int $userId;

    public string $profileXid;

    public string $xid;

    public string $serialNo;

    public int $skip = 0;

    public int $limit = 10;

    public string $sortBy = 'earliest';

    public string  $brandTypeModel;

    public string  $year;

    public array $locationMetadata;

    public array $submittedLocationMetadata;
}
