<?php

namespace Sanf\Core\Modules\Financing\Dto;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class FinancingSimulationPdfDto extends CamelCaseDataTransferObject
{
    public ?int $unitAmount;
    public ?int $financingAmount;
    public ?int $invoiceAmount;
    public ?int $downPaymentPercentage;
    public ?int $retentionAmount;
    public ?int $retentionPercentage;
    public ?int $downPaymentAmount;
    public ?string $firstInstallmentType;
    public ?int $interestPercentage;
    public ?int $tenor;
    public ?int $firstYearInsuranceAmount;
    public ?int $adminFeeAmount;
    public ?int $provisionAmount;
    public ?int $installmentPerMonth;
    public ?int $creditInsuranceAmount;
    public ?int $totalCreditAmount;
    public ?int $firstInstallmentAmount;
    public ?int $totalFirstPaymentAmount;
    public ?int $diskontoAmount;
    public ?int $disbursementAmount;
    public int $financingMethodId;
    public int $userId;
}
