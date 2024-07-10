<?php

namespace Sanf\Core\Modules\Financing\Dto;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class FinancingApplicationByScaninaRequestDto extends CamelCaseDataTransferObject
{
    public string $profileXid;
    public bool $isReceiveOffer;
    public FinancingApplicationPaymentByScaninaRequestDto $payment;
    public array $objects;
}
