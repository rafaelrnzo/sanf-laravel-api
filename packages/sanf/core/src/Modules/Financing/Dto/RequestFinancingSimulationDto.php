<?php

namespace Sanf\Core\Modules\Financing\Dto;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class RequestFinancingSimulationDto extends CamelCaseDataTransferObject
{
    public ?float $unitAmount;
    public ?float $financingAmount;
    public ?int $downPaymentPercentage;
    public ?float $downPaymentAmount;
    public ?string $firstInstallmentType;
    public ?int $interestPercentage;
    public ?int $tenor;
    public ?float $firstYearInsuranceAmount;
    public ?float $adminFeeAmount;
    public ?float $provisionAmount;
    public bool $isSendEmail;
    public bool $isDownloadPdf;
    public int $financingMethodId;
}
