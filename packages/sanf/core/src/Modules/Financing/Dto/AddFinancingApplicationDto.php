<?php


namespace Sanf\Core\Modules\Financing\Dto;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class AddFinancingApplicationDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public string $profileXid;
    public $profile;
    public int $financingFacilityId;
    public int $financingMethodId;
    //array of FinancingObjectDto
    public array $financingObjects;
    public bool $isReceiveOffer;
}
