<?php

namespace Sanf\Core\Modules\Financing\Dto;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class FinancingSimulationPdfDto extends CamelCaseDataTransferObject
{
    public ?float $unitAmount;
    public ?float $financingAmount;
    public ?float $invoiceAmount;
    public ?float $downPaymentPercentage;
    public ?float $retentionAmount;
    public ?float $retentionPercentage;
    public ?float $downPaymentAmount;
    public ?string $firstInstallmentType;
    public ?float $interestPercentage;
    public ?int $tenor;
    public ?float $firstYearInsuranceAmount;
    public ?float $adminFeeAmount;
    public ?float $provisionAmount;
    public ?float $installmentPerMonth;
    public ?float $creditInsuranceAmount;
    public ?float $totalCreditAmount;
    public ?float $firstInstallmentAmount;
    public ?float $totalFirstPaymentAmount;
    public ?float $diskontoAmount;
    public ?float $disbursementAmount;
    public int $financingMethodId;
    public int $userId;
}
