<?php


namespace Sanf\Core\Modules\Financing\Dto;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class AddFinancingApplicationDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public $profileXid;
    public $profile;
    public int $financingFacilityId;
    public int $financingMethodId;
    /**
     * Iterator of types:
     *
     * @var iterator<FinancingObjectDto>
     */
    public $financingObjects;
    public bool $isReceiveOffer;
}
